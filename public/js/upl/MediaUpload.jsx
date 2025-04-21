import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useUser } from '../context/UserContext';
import LocationPicker from './Observations/LocationPicker';
import Modal from './Observations/LPModal';
import LocationInput from './Observations/LocationInput';
import MediaCard from './MediaCard';
import BulkEditModal from './BulkEditModal';
import ProfileHeader from './ProfileHeader';

function MediaUpload() {
    const [observations, setObservations] = useState([]);
    const [selectedCards, setSelectedCards] = useState([]);
    const [locationName, setLocationName] = useState('');
    const [isLocationModalOpen, setIsLocationModalOpen] = useState(false);
    const [isBulkEditOpen, setIsBulkEditOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const [progress, setProgress] = useState(0);
    const [error, setError] = useState('');
    const [formData, setFormData] = useState({
        tujuan_pengamatan: 1,
        observer: '',
        additional_note: '',
        tgl_pengamatan: new Date().toISOString().split('T')[0],
        start_time: '',
        end_time: '',
        scientific_name: '',
        taxon_rank: '',
        kingdom: '',
    });
    const [bulkFormData, setBulkFormData] = useState(null);
    const [uploadProgress, setUploadProgress] = useState({});
    const [isConfirmModalOpen, setIsConfirmModalOpen] = useState(false);
    const [loadingMessage, setLoadingMessage] = useState('');
    const [qualityAssessments, setQualityAssessments] = useState({});
    const [showQualityModal, setShowQualityModal] = useState(false);
    const [searchResults, setSearchResults] = useState([]);
    const [isLoading, setIsLoading] = useState(false);

    const { user, setUser } = useUser();
    const navigate = useNavigate();

    useEffect(() => {
        const token = localStorage.getItem('jwt_token');
        const storedUser = {
            id: localStorage.getItem('user_id'),
            uname: localStorage.getItem('username'),
            totalObservations: localStorage.getItem('totalObservations'),
        };

        if (!token || !storedUser.id) {
            navigate('/login', { replace: true });
            return;
        }

        if (!user) {
            setUser(storedUser);
        }
    }, []);

    const handleFiles = async (files) => {
        setLoading(true);
        setProgress(0);
        setLoadingMessage('Memproses file...');

        try {
            const newObservations = [];
            let processedFiles = 0;

            for (const file of Array.from(files)) {
                const isImage = file.type.startsWith('image/');
                const isAudio = file.type.startsWith('audio/');

                if (isImage || isAudio) {
                    const observation = {
                        id: Date.now() + Math.random(),
                        file,
                        type: isImage ? 'image' : 'audio',
                        latitude: '',
                        longitude: '',
                        locationName: '',
                        description: '',
                        spectrogramUrl: null
                    };

                    if (isAudio) {
                        setLoadingMessage(`Membuat spektrogram untuk ${file.name}...`);
                        const formData = new FormData();
                        formData.append('media', file);

                        try {
                            const response = await fetch('http://127.0.0.1:8000/api/observations/generate-spectrogram', {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                                },
                                body: formData
                            });

                            if (response.ok) {
                                const data = await response.json();
                                observation.spectrogramUrl = data.spectrogramUrl;
                            }
                        } catch (error) {
                            console.error('Error generating spectrogram:', error);
                        }
                    }

                    newObservations.push(observation);
                }

                processedFiles++;
                setProgress((processedFiles / files.length) * 100);
                setLoadingMessage(`Memproses file ${processedFiles} dari ${files.length}...`);
            }

            await new Promise(resolve => setTimeout(resolve, 500));
            setProgress(100);
            setLoadingMessage('Selesai memproses file!');
            await new Promise(resolve => setTimeout(resolve, 500));

            setObservations(prev => [...prev, ...newObservations]);

        } catch (error) {
            setError('Gagal memproses file');
            console.error(error);
        } finally {
            setTimeout(() => {
                setLoading(false);
                setProgress(0);
                setLoadingMessage('');
            }, 500);
        }
    };

    const handleLocationSave = (lat, lng, name) => {
        setObservations(prev =>
            prev.map(obs =>
                selectedCards.includes(obs.id)
                    ? { ...obs, latitude: lat, longitude: lng, locationName: name }
                    : obs
            )
        );
        setLocationName(name);
        setIsLocationModalOpen(false);
    };

    const handleBulkEdit = (data) => {
        setBulkFormData(data);
        setObservations(prev =>
            prev.map(obs =>
                selectedCards.includes(obs.id)
                    ? { ...obs, ...data }
                    : obs
            )
        );
        setIsBulkEditOpen(false);
    };

    const handleCardSelect = (id) => {
        setSelectedCards(prev =>
            prev.includes(id)
                ? prev.filter(cardId => cardId !== id)
                : [...prev, id]
        );
    };

    const handleObservationUpdate = (id, data) => {
        setObservations(prev =>
            prev.map(obs =>
                obs.id === id ? { ...obs, ...data } : obs
            )
        );
    };

    const handleObservationDelete = (id) => {
        setObservations(prev => prev.filter(obs => obs.id !== id));
        setSelectedCards(prev => prev.filter(cardId => cardId !== id));
    };

    const handleConfirmSubmit = () => {
        if (!formData.scientific_name || !formData.observer) {
            setError('Silakan lengkapi data taksonomi dan observer');
            return;
        }
        setIsConfirmModalOpen(true);
    };

    const handleSubmit = async () => {
        setError('');
        setIsConfirmModalOpen(false);
        setLoading(true);
        setProgress(0);
        setLoadingMessage('Mengunggah data...');

        try {
            const progressInterval = setInterval(() => {
                setProgress(prev => {
                    if (prev >= 90) return 90;
                    return prev + 10;
                });
            }, 500);

            const newQualityAssessments = {};

            for (const observation of observations) {
                const submitFormData = new FormData();

                // Data dasar
                Object.keys(formData).forEach(key => {
                    if (formData[key]) { // Hanya kirim jika ada nilainya
                        submitFormData.append(key, formData[key]);
                    }
                });

                // Data observasi
                submitFormData.append('latitude', observation.latitude || '');
                submitFormData.append('longitude', observation.longitude || '');
                submitFormData.append('description', observation.description || '');
                submitFormData.append('habitat', observation.habitat || '');
                submitFormData.append('media', observation.file);

                // Log data yang akan dikirim
                console.log('Submitting data:', {
                    formData: Object.fromEntries(submitFormData.entries()),
                    observation
                });

                const response = await fetch('http://127.0.0.1:8000/api/observations', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                    },
                    body: submitFormData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengupload observasi');
                }

                // Simpan quality assessment
                if (data.quality_assessment) {
                    newQualityAssessments[observation.id] = data.quality_assessment;
                }

                setLoadingMessage(`Berhasil mengupload ${observation.file.name}`);
            }

            clearInterval(progressInterval);
            setProgress(100);
            setLoadingMessage('Semua observasi berhasil diupload!');
            setQualityAssessments(newQualityAssessments);
            setShowQualityModal(true);

        } catch (error) {
            console.error('Error uploading observations:', error);
            setError(error.message);
        } finally {
            setLoading(false);
            setProgress(0);
            setLoadingMessage('');
        }
    };

    const fetchTaxonomyInfo = async (scientificName) => {
        try {
            setIsLoading(true);
            const response = await fetch(`http://127.0.0.1:8000/api/taxonomy?scientific_name=${encodeURIComponent(scientificName)}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });

            if (!response.ok) {
                throw new Error('Gagal mendapatkan informasi taksonomi');
            }

            const data = await response.json();

            if (data.success) {
                setFormData(prev => ({
                    ...prev,
                    ...data.data
                }));
            }
        } catch (error) {
            console.error('Error fetching taxonomy:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const renderAdditionalForm = () => (
        <div className="mb-6 bg-white p-4 rounded-lg shadow">
            <h3 className="text-lg font-semibold mb-4">Data Observasi</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block mb-2">Tujuan Pengamatan</label>
                    <select
                        value={formData.tujuan_pengamatan}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            tujuan_pengamatan: parseInt(e.target.value)
                        }))}
                        className="w-full p-2 border rounded"
                    >
                        <option value="1">Penelitian</option>
                        <option value="2">Terencana/Terjadwal (Survey, Inventarisasi, Pengamatan Rutin, dll)</option>
                        <option value="3">Insidental/tidak ditujukan untuk pengamatan</option>
                        <option value="4">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label className="block mb-2">Observer</label>
                    <input
                        type="text"
                        value={formData.observer}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            observer: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-2">Tanggal Pengamatan</label>
                    <input
                        type="date"
                        value={formData.tgl_pengamatan}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            tgl_pengamatan: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                        required
                    />
                </div>

                <div>
                    <label className="block mb-2">Waktu Mulai</label>
                    <input
                        type="time"
                        value={formData.start_time}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            start_time: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-2">Waktu Selesai</label>
                    <input
                        type="time"
                        value={formData.end_time}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            end_time: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-2">Nama Taksa</label>
                    <div className="relative">
                        <input
                            type="text"
                            value={formData.scientific_name}
                            onChange={(e) => {
                                const value = e.target.value;
                                setFormData(prev => ({
                                    ...prev,
                                    scientific_name: value
                                }));

                                // Debounce pencarian
                                if (value.length > 2) {
                                    clearTimeout(window.searchTimeout);
                                    window.searchTimeout = setTimeout(() => {
                                        fetchTaxonomyInfo(value);
                                    }, 500);
                                }
                            }}
                            className="w-full p-2 border rounded"
                            required
                        />
                        {isLoading && (
                            <div className="absolute right-2 top-2">
                                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                            </div>
                        )}
                    </div>

                    {/* Tampilkan informasi taksonomi */}
                    {formData.kingdom && (
                        <div className="mt-2 text-sm text-gray-600">
                            <div className="grid grid-cols-2 gap-2">
                                <div>Kingdom: {formData.kingdom}</div>
                                <div>Phylum: {formData.phylum}</div>
                                <div>Class: {formData.class}</div>
                                <div>Order: {formData.order}</div>
                                <div>Family: {formData.family}</div>
                                <div>Genus: {formData.genus}</div>
                                <div>Species: {formData.species}</div>
                                <div>Rank: {formData.taxon_rank}</div>
                            </div>
                        </div>
                    )}
                </div>


            </div>

            <div className="mt-4">
                <label className="block mb-2">Catatan Tambahan</label>
                <textarea
                    value={formData.additional_note}
                    onChange={(e) => setFormData(prev => ({
                        ...prev,
                        additional_note: e.target.value
                    }))}
                    className="w-full p-2 border rounded"
                    rows="3"
                />
            </div>
        </div>
    );

    const QualityAssessmentModal = ({ isOpen, onClose, assessments }) => {
        if (!isOpen) return null;

        const getGradeColor = (grade) => {
            switch (grade.toLowerCase()) {
                case 'research grade':
                    return 'bg-green-100 text-green-800';
                case 'needs id':
                    return 'bg-yellow-100 text-yellow-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        };

        return (
            <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                <div className="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                    <h2 className="text-xl font-semibold mb-4">Hasil Penilaian Kualitas</h2>

                    {Object.entries(assessments).map(([obsId, assessment]) => (
                        <div key={obsId} className="mb-4 p-4 border rounded">
                            <div className={`inline-block px-3 py-1 rounded-full text-sm font-semibold mb-2 ${getGradeColor(assessment.grade)}`}>
                                {assessment.grade}
                            </div>

                            <div className="grid grid-cols-2 gap-2 text-sm">
                                <div className="flex items-center">
                                    <span className={assessment.has_date ? 'text-green-500' : 'text-red-500'}>
                                        {assessment.has_date ? '✓' : '✗'} Tanggal
                                    </span>
                                </div>
                                <div className="flex items-center">
                                    <span className={assessment.has_location ? 'text-green-500' : 'text-red-500'}>
                                        {assessment.has_location ? '✓' : '✗'} Lokasi
                                    </span>
                                </div>
                                <div className="flex items-center">
                                    <span className={assessment.has_media ? 'text-green-500' : 'text-red-500'}>
                                        {assessment.has_media ? '✓' : '✗'} Media
                                    </span>
                                </div>
                                <div className="flex items-center">
                                    <span className={assessment.is_wild ? 'text-green-500' : 'text-red-500'}>
                                        {assessment.is_wild ? '✓' : '✗'} Liar
                                    </span>
                                </div>
                                <div className="flex items-center">
                                    <span className={assessment.location_accurate ? 'text-green-500' : 'text-red-500'}>
                                        {assessment.location_accurate ? '✓' : '✗'} Lokasi Akurat
                                    </span>
                                </div>
                                <div className="flex items-center">
                                    <span className={assessment.recent_evidence ? 'text-green-500' : 'text-red-500'}>
                                        {assessment.recent_evidence ? '✓' : '✗'} Bukti Terbaru
                                    </span>
                                </div>
                            </div>
                        </div>
                    ))}

                    <div className="mt-4 flex justify-end">
                        <button
                            onClick={onClose}
                            className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        );
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <ProfileHeader userData={{
                uname: localStorage.getItem('username'),
                totalObservations: localStorage.getItem('totalObservations')
            }} />

            <div className="container mx-auto px-4 py-8 mt-16">
                {/* File Drop Zone */}
                <div
                    className="border-2 border-dashed border-gray-300 p-8 text-center rounded-lg mb-4"
                    onDrop={(e) => {
                        e.preventDefault();
                        handleFiles(e.dataTransfer.files);
                    }}
                    onDragOver={(e) => e.preventDefault()}
                >
                    <input
                        type="file"
                        multiple
                        accept="image/*,audio/*"
                        onChange={(e) => handleFiles(e.target.files)}
                        className="hidden"
                        id="fileInput"
                    />
                    <label
                        htmlFor="fileInput"
                        className="cursor-pointer text-blue-600 hover:text-blue-800"
                    >
                        Klik untuk memilih atau seret file ke sini
                    </label>
                </div>

                {/* Form tambahan jika ada observations */}
                {observations.length > 0 && renderAdditionalForm()}

                {/* Bulk Actions */}
                {selectedCards.length > 0 && (
                    <div className="mb-4 p-4 bg-gray-100 rounded-lg flex gap-2">
                        <button
                            onClick={() => setIsLocationModalOpen(true)}
                            className="bg-blue-500 text-white px-4 py-2 rounded"
                        >
                            Set Lokasi ({selectedCards.length})
                        </button>
                        <button
                            onClick={() => setIsBulkEditOpen(true)}
                            className="bg-purple-500 text-white px-4 py-2 rounded"
                        >
                            Edit All ({selectedCards.length})
                        </button>
                    </div>
                )}

                {/* Media Cards Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {observations.map(obs => (
                        <MediaCard
                            key={obs.id}
                            observation={obs}
                            isSelected={selectedCards.includes(obs.id)}
                            onSelect={() => handleCardSelect(obs.id)}
                            onUpdate={(data) => handleObservationUpdate(obs.id, data)}
                            onDelete={() => handleObservationDelete(obs.id)}
                            bulkFormData={bulkFormData}
                            uploadProgress={uploadProgress[obs.id] || 0}
                        />
                    ))}
                </div>

                {/* Modals */}
                <Modal
                    isOpen={isLocationModalOpen}
                    onClose={() => setIsLocationModalOpen(false)}
                >
                    <LocationPicker onSave={handleLocationSave} />
                </Modal>

                <BulkEditModal
                    isOpen={isBulkEditOpen}
                    onClose={() => setIsBulkEditOpen(false)}
                    onSave={handleBulkEdit}
                    selectedItems={selectedCards.map(id => observations.find(obs => obs.id === id))}
                />

                {/* Submit Button */}
                {observations.length > 0 && (
                    <button
                        onClick={handleConfirmSubmit}
                        className="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-full shadow-lg"
                    >
                        Upload {observations.length} Observasi
                    </button>
                )}

                {/* Modal Konfirmasi */}
                {isConfirmModalOpen && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                        <div className="bg-white p-6 rounded shadow-lg">
                            <h2 className="text-xl font-semibold mb-4">Konfirmasi Upload</h2>
                            <p>Apakah Anda yakin ingin mengupload {observations.length} observasi ini?</p>
                            <div className="mt-4 flex justify-between">
                                <button
                                    onClick={handleSubmit}
                                    className="bg-green-500 text-white p-2 rounded hover:bg-green-600"
                                >
                                    Ya, Upload
                                </button>
                                <button
                                    onClick={() => setIsConfirmModalOpen(false)}
                                    className="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* Loading Indicator */}
                {loading && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                        <div className="bg-white p-6 rounded-lg text-center">
                            <div className="mb-4 text-lg font-medium">{loadingMessage}</div>
                            <div className="w-80 h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-blue-500 rounded-full transition-all duration-300 ease-out"
                                    style={{ width: `${progress}%` }}
                                />
                            </div>
                            <div className="mt-2 text-sm text-gray-600">
                                {Math.round(progress)}%
                            </div>
                        </div>
                    </div>
                )}

                {/* Error Display */}
                {error && (
                    <div className="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded shadow-lg">
                        {error}
                    </div>
                )}

                {/* Quality Assessment Modal */}
                <QualityAssessmentModal
                    isOpen={showQualityModal}
                    onClose={() => {
                        setShowQualityModal(false);
                        // Reset form setelah menutup modal
                        setObservations([]);
                        setSelectedCards([]);
                        setFormData({
                            tujuan_pengamatan: 1,
                            observer: '',
                            additional_note: '',
                            tgl_pengamatan: new Date().toISOString().split('T')[0],
                            start_time: '',
                            end_time: '',
                            scientific_name: '',
                            taxon_rank: '',
                            kingdom: '',
                        });
                    }}
                    assessments={qualityAssessments}
                />
            </div>
        </div>
    );
}

export default MediaUpload;
