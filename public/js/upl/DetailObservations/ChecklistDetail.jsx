import React, { useState, useEffect, useRef } from 'react';
import { useParams } from 'react-router-dom';
import { useUser } from '../../context/UserContext';
import Pusher from 'pusher-js';
import MediaViewer from './MediaViewer';
import ChecklistMap from './ChecklistMap';
import QualityAssessment from './QualityAssessment';
import TabPanel from './TabPanel';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faMapMarkerAlt } from '@fortawesome/free-solid-svg-icons';
import { apiFetch } from '../../utils/api';
function ChecklistDetail() {
    const [checklist, setChecklist] = useState(null);
    const [identifications, setIdentifications] = useState([]);
    const [locationVerifications, setLocationVerifications] = useState([]);
    const [wildStatusVotes, setWildStatusVotes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [selectedTaxon, setSelectedTaxon] = useState(null);
    const [identificationForm, setIdentificationForm] = useState({
        taxon_id: '',
        identification_level: 'species',
        comment: ''
    });
    const [comments, setComments] = useState([]);
    const [newComment, setNewComment] = useState('');
    const [qualityAssessment, setQualityAssessment] = useState({
        has_date: false,
        has_location: false,
        has_media: false,
        is_wild: true,
        location_accurate: true,
        recent_evidence: true,
        related_evidence: true,
        community_id_level: '',
        can_be_improved: null
    });
    const [activeTab, setActiveTab] = useState('identification');
    const [activeIndex, setActiveIndex] = useState(0);
    const swiperRef = useRef(null);
    const [spectrogramSwiper, setSpectrogramSwiper] = useState(null);
    const [activeAudioIndex, setActiveAudioIndex] = useState(0);
    const audioRefs = useRef([]);
    const progressRefs = useRef([]);
    const spectrogramSwipers = useRef([]);
    const [locationName, setLocationName] = useState('Memuat lokasi...');

    const { id } = useParams();
    const { user } = useUser();

    useEffect(() => {
        fetchChecklistDetail();
        fetchComments();
        fetchQualityAssessment();

        // Inisialisasi Pusher
        const pusher = new Pusher('2d50c7dd083d072bcc27', {
            cluster: 'ap1',
        });

        // Berlangganan ke channel
        const channel = pusher.subscribe('checklist');

        // Mendengarkan event komentar
        channel.bind('CommentAdded', function(data) {
            setComments(prev => [...prev, data.comment]);
        });

        // Mendengarkan event quality assessment
        channel.bind('QualityAssessmentUpdated', function(data) {
            setQualityAssessment(data.assessment);
        });

        // Mendengarkan event identifikasi
        channel.bind('IdentificationUpdated', function(data) {
            setIdentifications(prev => prev.map(ident =>
                ident.id === data.identificationId ? { ...ident, agreement_count: data.agreementCount, user_agreed: data.userAgreed } : ident
            ));
        });

        // Cleanup
        return () => {
            channel.unbind_all();
            channel.unsubscribe();
        };
    }, []);
    const fetchChecklistDetail = async () => {
        try {
            const response = await apiFetch(`/observations/${id}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });

            const data = await response.json();
            if (data.success) {
                // Tambahkan informasi persetujuan ke setiap identifikasi
                const identificationsWithAgreements = data.data.identifications.map(identification => ({
                    ...identification,
                    agreement_count: identification.agreement_count || 0,
                    user_agreed: identification.user_agreed || false
                }));

                setChecklist({
                    ...data.data.checklist,
                    quality_grade: data.data.checklist.grade,
                    iucn_status: data.data.checklist.iucn_status,
                    agreement_count: data.data.checklist.agreement_count
                });
                setIdentifications(identificationsWithAgreements);
                setLocationVerifications(data.data.location_verifications);
                setWildStatusVotes(data.data.wild_status_votes);
            } else {
                setError('Gagal memuat data checklist');
            }
        } catch (error) {
            console.error('Error fetching checklist detail:', error);
            setError('Terjadi kesalahan saat memuat data');
        } finally {
            setLoading(false);
        }
    };
    const searchTaxa = async (query) => {
        if (query.length < 3) return;

        try {
            const response = await apiFetch(`/taxa/search?q=${query}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });

            const data = await response.json();
            if (data.success) {
                setSearchResults(data.data);
            }
        } catch (error) {
            console.error('Error searching taxa:', error);
        }
    };

    const handleIdentificationSubmit = async (e, photo) => {
        e.preventDefault();

        try {
            const formData = new FormData();
            formData.append('taxon_id', identificationForm.taxon_id);
            formData.append('identification_level', identificationForm.identification_level);
            if (identificationForm.comment) {
                formData.append('comment', identificationForm.comment);
            }
            if (photo) {
                formData.append('photo', photo);
            }

            const response = await apiFetch(
                `/observations/${id}/identifications`,
                {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                    },
                    body: formData
                }
            );

            const data = await response.json();

            if (data.success) {
                // Update identifications state dengan foto baru
                const newIdentification = {
                    ...data.data,
                    user_id: user.id,
                    identifier_name: user.name,
                    scientific_name: selectedTaxon.scientific_name,
                    identification_level: identificationForm.identification_level,
                    comment: identificationForm.comment,
                    photo_url: data.data.photo_url,
                    created_at: new Date().toISOString(),
                    agreement_count: 0,
                    user_agreed: false,
                    is_withdrawn: false
                };

                setIdentifications(prev => [newIdentification, ...prev]);
                setIdentificationForm({ taxon_id: '', identification_level: '', comment: '' });
                setSelectedTaxon(null);
            }
        } catch (error) {
            console.error('Error submitting identification:', error);
        }
    };

    const handleLocationVerify = async (isAccurate, comment = '') => {
        try {
            const response = await apiFetch(`/observations/${id}/verify-location`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_accurate: isAccurate, comment })
            });

            if (response.ok) {
                fetchChecklistDetail();
            }
        } catch (error) {
            console.error('Error verifying location:', error);
        }
    };

    const handleWildStatusVote = async (isWild, comment = '') => {
        try {
            const response = await apiFetch(`/observations/${id}/vote-wild`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_wild: isWild, comment })
            });

            if (response.ok) {
                fetchChecklistDetail();
            }
        } catch (error) {
            console.error('Error voting wild status:', error);
        }
    };

    const fetchComments = async () => {
        try {
            const response = await apiFetch(`/observations/${id}/comments`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });

            const data = await response.json();
            if (data.success) {
                setComments(data.data);
            }
        } catch (error) {
            console.error('Error fetching comments:', error);
        }
    };

    const addComment = async (e) => {
        e.preventDefault();
        try {
            const response = await apiFetch(`/observations/${id}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: JSON.stringify({ comment: newComment })
            });

            const data = await response.json();
            if (data.success) {
                setNewComment('');
                fetchComments();
            }
        } catch (error) {
            console.error('Error adding comment:', error);
        }
    };
    const rateChecklist = async (grade) => {
        try {
            const response = await apiFetch(`/observations/${id}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: JSON.stringify({ grade })
            });

            const data = await response.json();
            if (data.success) {
                fetchChecklistDetail();
            }
        } catch (error) {
            console.error('Error rating checklist:', error);
        }
    };

    const fetchQualityAssessment = async () => {
        try {
            const response = await apiFetch(`/observations/${id}/assess-quality`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });

            const data = await response.json();
            if (data.success) {
                setQualityAssessment(data.data);

                if (data.data.grade && (!checklist || checklist.quality_grade !== data.data.grade)) {
                    setChecklist(prev => ({
                        ...prev,
                        quality_grade: data.data.grade
                    }));
                }
            }
        } catch (error) {
            console.error('Error fetching quality assessment:', error);
        }
    };

    const handleQualityAssessmentChange = async (criteria, value) => {
        try {
            setQualityAssessment(prevAssessment => ({
                ...prevAssessment,
                [criteria]: value
            }));

            const response = await apiFetch(`/observations/${id}/quality-assessment/${criteria}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: JSON.stringify({ value })
            });

            const data = await response.json();

            if (data.success) {
                if (data.data.grade) {
                    setChecklist(prev => ({
                        ...prev,
                        quality_grade: data.data.grade
                    }));
                }
            } else {
                setQualityAssessment(prevAssessment => ({
                    ...prevAssessment,
                    [criteria]: !value
                }));
                console.error('Gagal memperbarui penilaian:', data.message);
            }
        } catch (error) {
            setQualityAssessment(prevAssessment => ({
                ...prevAssessment,
                [criteria]: !value
            }));
            console.error('Error updating quality assessment:', error);
        }
    };

    const handleImprovementChange = async (canImprove) => {
        try {
            const response = await apiFetch(`/observations/${id}/improvement-status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: JSON.stringify({
                    can_be_improved: canImprove
                })
            });

            const data = await response.json();
            if (data.success) {
                setQualityAssessment(prev => ({
                    ...prev,
                    can_be_improved: canImprove
                }));

                // Refresh quality assessment untuk mendapatkan grade terbaru
                fetchQualityAssessment();
            } else {
                console.error('Gagal memperbarui status improvement:', data.message);
            }
        } catch (error) {
            console.error('Error updating improvement status:', error);
        }
    };

    const handleAgreeWithIdentification = async (identificationId) => {
        try {
            const response = await apiFetch(`/observations/${id}/identifications/${identificationId}/agree`, {
                method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                        'Content-Type': 'application/json'
                    }
                }
            );

            const data = await response.json();

            if (data.success) {
                // Update state identifications dengan data terbaru
                setIdentifications(prevIdentifications =>
                    prevIdentifications.map(ident =>
                        ident.id === identificationId
                            ? {
                                ...ident,
                                agreement_count: data.data.agreement_count,
                                user_agreed: data.data.user_agreed
                            }
                            : ident
                    )
                );
            } else {
                console.error('Gagal menyetujui identifikasi:', data.message);
            }
        } catch (error) {
            console.error('Error saat menyetujui identifikasi:', error);
        }
    };

    const getLocationName = async (latitude, longitude) => {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`
            );
            const data = await response.json();
            return data.display_name;
        } catch (error) {
            console.error('Error fetching location name:', error);
            return 'Gagal memuat nama lokasi';
        }
    };

    useEffect(() => {
        if (checklist?.latitude && checklist?.longitude) {
            getLocationName(checklist.latitude, checklist.longitude)
                .then(name => setLocationName(name));
        }
    }, [checklist]);

    const handleWithdrawIdentification = async (identificationId) => {
        try {
            const response = await apiFetch(`/observations/${id}/identifications/${identificationId}/withdraw`, {
                method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                        'Content-Type': 'application/json'
                    }
                }
            );
    
            const data = await response.json();
    
            if (data.success) {
                // Update state identifications untuk menandai identifikasi yang ditarik
                setIdentifications(prevIdentifications =>
                    prevIdentifications.map(ident => {
                        if (ident.id === identificationId) {
                            return { ...ident, is_withdrawn: true };
                        }
                        // Hapus semua persetujuan yang terkait dengan identifikasi ini
                        if (ident.agrees_with_id === identificationId) {
                            return null; // Identifikasi yang menyetujui akan dihapus
                        }
                        return ident;
                    }).filter(Boolean) // Hapus semua nilai null
                );
    
                // Refresh data checklist untuk mendapatkan status terbaru
                await fetchChecklistDetail();
            } else {
                console.error('Gagal menarik identifikasi:', data.message);
            }
        } catch (error) {
            console.error('Error saat menarik identifikasi:', error);
        }
    };
        const handleCancelAgreement = async (identificationId) => {
        try {
            const response = await apiFetch(`/observations/${id}/identifications/${identificationId}/cancel-agreement`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                setIdentifications(prevIdentifications =>
                    prevIdentifications.map(ident =>
                        ident.id === identificationId
                            ? { ...ident, agreement_count: data.data.agreement_count, user_agreed: false }
                            : ident
                    )
                );
            } else {
                console.error('Gagal membatalkan persetujuan:', data.message);
            }
        } catch (error) {
            console.error('Error saat membatalkan persetujuan:', error);
        }
    };

    const handleDisagreeWithIdentification = async (identificationId, comment) => {
        try {
            const formData = new FormData();
            formData.append('comment', comment || '');
            if (selectedTaxon) {
                formData.append('taxon_id', selectedTaxon.id);
                formData.append('identification_level', selectedTaxon.taxon_rank);
            }
    
            const response = await apiFetch(`/observations/${id}/identifications/${identificationId}/disagree`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                    },
                    body: formData
                }
            );
    
            // Tambahkan log untuk memeriksa respons
            const text = await response.text();
            console.log('Response text:', text);
    
            const data = JSON.parse(text);
            if (data.success) {
                setIdentifications(prevIdentifications => [
                    ...prevIdentifications,
                    {
                        ...data.data,
                        user_disagreed: true,
                        agrees_with_id: null
                    }
                ]);
    
                await fetchChecklistDetail();
            } else {
                console.error('Gagal menolak identifikasi:', data.message);
            }
        } catch (error) {
            console.error('Error saat menolak identifikasi:', error);
        }
    };
        if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-lg text-gray-600">Memuat...</div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-lg text-red-600">Error: {error}</div>
            </div>
        );
    }

    return (
        <div className="container mx-auto mt-10 px-4 py-8">
                        <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div className="flex justify-between items-start">
                    <div>
                        <h1 className="text-2xl font-bold mb-2">
                            {checklist?.scientific_name || 'Nama tidak tersedia'}
                        </h1>
                        <div className="text-gray-600">
                            Diamati oleh {checklist?.observer_name || 'Pengamat tidak diketahui'} pada{' '}
                            {checklist?.created_at ? new Date(checklist.created_at).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}
                        </div>
                    </div>
                    <div className={`px-4 py-2 rounded-full text-sm font-semibold
                        ${checklist?.quality_grade === 'research grade' ? 'bg-green-100 text-green-800' :
                          checklist?.quality_grade === 'needs ID' ? 'bg-yellow-100 text-yellow-800' :
                          'bg-gray-100 text-gray-800'}`}>
                        {checklist?.quality_grade === 'research grade' ? 'ID Lengkap' :
                         checklist?.quality_grade === 'needs ID' ? 'Bantu Iden' :
                         'Casual'}
                    </div>
                </div>
            </div>

            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 className="text-xl font-semibold mb-4">Media</h2>
                        <MediaViewer checklist={checklist} />
                    </div>
                    <div>
                        <h2 className="text-xl font-semibold mb-4">Peta Sebaran</h2>
                        <ChecklistMap checklist={checklist} />
                        <div className="text-sm text-gray-500 mt-10 text-center">
                            <p><FontAwesomeIcon icon={faMapMarkerAlt} className="text-red-500" /> {locationName}</p>
                        </div>
                    </div>
                </div>
            </div>


            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-6">
<TabPanel
    id={id}
    activeTab={activeTab}
    setActiveTab={setActiveTab}
    comments={comments}
    identifications={identifications}
    newComment={newComment}
    setNewComment={setNewComment}
    addComment={addComment}
    handleIdentificationSubmit={handleIdentificationSubmit}
    searchTaxa={searchTaxa}
    searchResults={searchResults}
    selectedTaxon={selectedTaxon}
    setSelectedTaxon={setSelectedTaxon}
    identificationForm={identificationForm}
    setIdentificationForm={setIdentificationForm}
    handleLocationVerify={handleLocationVerify}
    handleWildStatusVote={handleWildStatusVote}
    locationVerifications={locationVerifications}
    wildStatusVotes={wildStatusVotes}
    handleAgreeWithIdentification={handleAgreeWithIdentification}
    handleWithdrawIdentification={handleWithdrawIdentification}
    handleCancelAgreement={handleCancelAgreement}
    handleDisagreeWithIdentification={handleDisagreeWithIdentification}
    user={user}
    checklist={checklist}
/>
                </div>

                <div className="space-y-6">
                    <div className="bg-white rounded-lg shadow-lg p-6">
                        <h2 className="text-xl font-semibold mb-4">Detail Taksonomi</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <div className="font-semibold">Kingdom</div>
                                <div>{checklist?.kingdom || '-'}</div>
                            </div>
                            <div>
                                <div className="font-semibold">Family</div>
                                <div>{checklist?.family || '-'}</div>
                            </div>
                            <div>
                                <div className="font-semibold">Genus</div>
                                <div>{checklist?.genus || '-'}</div>
                            </div>
                            <div>
                                <div className="font-semibold">Species</div>
                                <div>{checklist?.species || '-'}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <QualityAssessment
                checklist={checklist}
                qualityAssessment={qualityAssessment}
                identifications={identifications}
                handleQualityAssessmentChange={handleQualityAssessmentChange}
                handleImprovementChange={handleImprovementChange}
            />
        </div>
    );
}

export default ChecklistDetail;
