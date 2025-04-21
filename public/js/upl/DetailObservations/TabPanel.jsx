import React, { useState } from 'react';
import ReactQuill from 'react-quill';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faComments,
    faSearch,
    faCheckCircle,
    faMapMarkerAlt,
    faPaw
} from '@fortawesome/free-solid-svg-icons';
import 'react-quill/dist/quill.snow.css';
import { apiFetch } from '../../utils/api';
function TabPanel({
    id,
    activeTab,
    setActiveTab,
    comments,
    identifications,
    newComment,
    setNewComment,
    addComment,
    handleIdentificationSubmit,
    searchTaxa,
    searchResults,
    selectedTaxon,
    setSelectedTaxon,
    identificationForm,
    setIdentificationForm,
    handleAgreeWithIdentification,
    handleWithdrawIdentification,
    handleCancelAgreement,
    handleDisagreeWithIdentification,
    user,
    checklist
}) {
    const [searchQuery, setSearchQuery] = useState('');
    const [showDisagreeModal, setShowDisagreeModal] = useState(false);
    const [disagreeComment, setDisagreeComment] = useState('');
    const [selectedIdentificationId, setSelectedIdentificationId] = useState(null);
    const [identificationPhoto, setIdentificationPhoto] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);

    const tabs = [
        { id: 'identification', label: 'Identifikasi', icon: faSearch },
        { id: 'comments', label: 'Komentar', icon: faComments }
    ];

    const handleSearch = (query) => {
        setSearchQuery(query);
        if (query.length >= 3) {
            searchTaxa(query);
        }
    };

    const handleTaxonSelect = (taxon) => {
        setSelectedTaxon(taxon);
        setIdentificationForm(prev => ({
            ...prev,
            taxon_id: taxon.id,
            identification_level: taxon.taxon_rank
        }));
        setSearchQuery('');
    };
    const handleDisagreeSubmit = async (identificationId) => {
        try {
            const response = await apiFetch(`/observations/${id}/identifications/${identificationId}/disagree`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ comment: disagreeComment })
            });

            const data = await response.json();
            if (data.success) {
                setIdentifications(prevIdentifications =>
                    prevIdentifications.map(ident =>
                        ident.id === identificationId
                            ? { ...ident, user_disagreed: true }
                            : ident
                    )
                );
                setShowDisagreeModal(false);
            } else {
                console.error('Gagal menolak identifikasi:', data.message);
            }
        } catch (error) {
            console.error('Error saat menolak identifikasi:', error);
        }
    };

    const handleDisagreeClick = (identificationId) => {
        setSelectedIdentificationId(identificationId);
        setShowDisagreeModal(true);
    };

    const handlePhotoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setIdentificationPhoto(file);
            setPhotoPreview(URL.createObjectURL(file));
        }
    };


    const renderIdentifications = () => {
        const sortedIdentifications = [...identifications].sort((a, b) => {
            if (a.is_first) return -1;
            if (b.is_first) return 1;
            return new Date(b.created_at) - new Date(a.created_at);
        });

        return sortedIdentifications.map((identification) => (
            <div key={identification.id} className="bg-white rounded-lg shadow p-4 mb-4">
                <div className="flex justify-between items-start">
                    <div className="flex-grow">
                        <div className="mb-2">
                            <div className="flex items-center">
                                <span className={identification.is_withdrawn ? 'line-through text-gray-400' : 'text-lg font-semibold'}>
                                    {identification.scientific_name || '-'}
                                </span>
                                {Boolean(identification.is_withdrawn) && (
                                    <span className="text-sm text-red-600 ml-2">(Ditarik)</span>
                                )}
                            </div>
                        </div>

                        <div className="mt-2 space-y-1">
                            <p className="text-sm text-gray-600">
                                Level Identifikasi: {identification.identification_level}
                            </p>
                            <p className="text-sm text-gray-600">
                                Diidentifikasi oleh {identification.identifier_name}
                            </p>
                            <p className="text-sm text-gray-600">
                                Tanggal: {new Date(identification.created_at).toLocaleDateString('id-ID')}
                            </p>
                        </div>

                        {identification.comment && (
                            <div className="mt-3 text-gray-700 bg-gray-50 p-3 rounded">
                                <p className="text-sm font-medium mb-1">Catatan:</p>
                                <p>{identification.comment}</p>
                            </div>
                        )}
                    </div>

                    <div className="flex flex-col items-end">
                        <div className="text-sm font-medium mb-2">
                            {identification.agreement_count > 0 && (
                                <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    {identification.agreement_count} setuju
                                </span>
                            )}
                        </div>

                        <div className="flex space-x-2">
                {!identification.is_withdrawn && !identification.agrees_with_id && (
                    <>
                        {identification.user_id !== user?.id && !identification.user_agreed && (
                            <>
                                <button
                                    onClick={() => handleAgreeWithIdentification(identification.id)}
                                    className="px-3 py-1 rounded bg-green-100 text-green-800 hover:bg-green-200"
                                >
                                    Setuju
                                </button>
                                <button
                                    onClick={() => handleDisagreeClick(identification.id)}
                                    className="px-3 py-1 rounded bg-red-100 text-red-800 hover:bg-red-200"
                                >
                                    Tolak
                                </button>
                            </>
                        )}
                        {identification.user_agreed && (
                            <button
                                onClick={() => handleCancelAgreement(identification.id)}
                                className="px-3 py-1 rounded bg-gray-100 text-gray-800 hover:bg-gray-200"
                            >
                                Batal Setuju
                            </button>
                        )}
                        {/* Tambahkan tombol tarik identifikasi */}
                        {identification.user_id === user?.id && (
                            <button
                                onClick={() => handleWithdrawIdentification(identification.id)}
                                className="px-3 py-1 rounded bg-yellow-100 text-yellow-800 hover:bg-yellow-200"
                            >
                                Tarik Identifikasi
                            </button>
                        )}
                    </>
                )}
            </div>
                                </div>
                </div>

                {identification.photo_url && (
                    <div className="mt-3">
                        <img
                            src={identification.photo_url}
                            alt="Foto identifikasi"
                            className="max-h-48 w-auto rounded"
                        />
                    </div>
                )}
            </div>
        ));
    };
    return (
        <div className="bg-white rounded-lg shadow-lg p-6">
            <div className="border-b mb-4">
                <div className="flex space-x-4">
                    {tabs.map(tab => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`pb-2 px-4 flex items-center space-x-2 ${
                                activeTab === tab.id
                                    ? 'border-b-2 border-blue-500 text-blue-500'
                                    : 'text-gray-500 hover:text-gray-700'
                            }`}
                        >
                            <FontAwesomeIcon icon={tab.icon} />
                            <span>{tab.label}</span>
                        </button>
                    ))}
                </div>
            </div>

            <div className="mt-4">
                {activeTab === 'identification' && (
                    <div>
                        <span className="text-sm text-gray-500">Bantu Pengamat memastikan identifikasinya,
                        dengan memberi komentar, foto pembanding
                        atau usul nama.</span>
                        <div className="mb-4">
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => handleSearch(e.target.value)}
                                placeholder="Cari takson..."
                                className="w-full p-2 border rounded"
                            />
                            {searchQuery.length >= 3 && searchResults.length > 0 && (
                                <div className="mt-2 border rounded max-h-48 overflow-y-auto">
                                    {searchResults.map(taxon => (
                                        <div
                                            key={taxon.id}
                                            onClick={() => handleTaxonSelect(taxon)}
                                            className="p-2 hover:bg-gray-100 cursor-pointer"
                                        >
                                            {taxon.scientific_name}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        {selectedTaxon && (
                            <form onSubmit={(e) => {
                                e.preventDefault();
                                handleIdentificationSubmit(e, identificationPhoto);
                                setIdentificationPhoto(null);
                                setPhotoPreview(null);
                            }} className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Takson Terpilih
                                    </label>
                                    <div className="mt-1 p-2 border rounded bg-gray-50">
                                        {selectedTaxon.scientific_name}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Foto Pendukung (Opsional)
                                    </label>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        onChange={handlePhotoChange}
                                        className="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100"
                                    />
                                    {photoPreview && (
                                        <div className="mt-2">
                                            <img
                                                src={photoPreview}
                                                alt="Preview"
                                                className="h-32 w-auto object-cover rounded"
                                            />
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setIdentificationPhoto(null);
                                                    setPhotoPreview(null);
                                                }}
                                                className="mt-1 text-sm text-red-600 hover:text-red-800"
                                            >
                                                Hapus foto
                                            </button>
                                        </div>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Komentar (Opsional)
                                    </label>
                                    <textarea
                                        value={identificationForm.comment}
                                        onChange={(e) => setIdentificationForm(prev => ({
                                            ...prev,
                                            comment: e.target.value
                                        }))}
                                        maxLength={500}
                                        className="mt-1 block w-full p-2 border rounded"
                                        rows={3}
                                    />
                                </div>

                                <button
                                    type="submit"
                                    className="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
                                >
                                    Kirim Identifikasi
                                </button>
                            </form>
                        )}

                        <div className="mt-6">
                            {identifications.length > 0 && (
                                <div className="mb-4 p-4 bg-gray-50 rounded">
                                    <h3 className="font-medium">Identifikasi Saat Ini</h3>
                                    <p>{checklist.scientific_name}</p>
                                    <p className="text-sm text-gray-600">
                                        Disetujui oleh {checklist.agreement_count} pengamat
                                    </p>
                                    {checklist.iucn_status && (
                                        <div className="mt-2">
                                            <span className="text-sm font-medium">Status IUCN: </span>
                                            <span className={`px-2 py-1 rounded text-sm ${
                                                checklist.iucn_status.toLowerCase().includes('endangered')
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-green-100 text-green-800'
                                            }`}>
                                                {checklist.iucn_status}
                                            </span>
                                        </div>
                                    )}
                                </div>
                            )}

                            {renderIdentifications()}
                        </div>
                    </div>
                )}

                {activeTab === 'comments' && (
                    <div>
                        <div className="mb-4">
                            <ReactQuill
                                value={newComment}
                                onChange={setNewComment}
                                placeholder="Tulis komentar..."
                            />
                            <button
                                onClick={addComment}
                                className="mt-2 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
                            >
                                Kirim Komentar
                            </button>
                        </div>

                        <div className="space-y-4">
                            {comments.map(comment => (
                                <div key={comment.id} className="border-b pb-4">
                                    <div className="flex justify-between">
                                        <span className="font-medium">{comment.user_name}</span>
                                        <span className="text-sm text-gray-500">
                                            {new Date(comment.created_at).toLocaleDateString('id-ID')}
                                        </span>
                                    </div>
                                    <div className="mt-2" dangerouslySetInnerHTML={{ __html: comment.comment }} />
                                </div>
                            ))}
                        </div>
                    </div>
                )}

            </div>

            {/* Modal untuk menolak identifikasi */}
            {showDisagreeModal && (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div className="bg-white rounded-lg p-6 w-96">
            <h3 className="text-lg font-semibold mb-4">Tolak Identifikasi</h3>

            {/* Tambahkan pencarian dan pemilihan takson */}
            <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Takson
                </label>
                <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => handleSearch(e.target.value)}
                    placeholder="Cari takson..."
                    className="w-full p-2 border rounded mb-2"
                />

                {searchResults.length > 0 && (
                    <div className="max-h-40 overflow-y-auto border rounded">
                        {searchResults.map((taxon) => (
                            <div
                                key={taxon.id}
                                onClick={() => handleTaxonSelect(taxon)}
                                className="p-2 hover:bg-gray-100 cursor-pointer"
                            >
                                {taxon.scientific_name}
                            </div>
                        ))}
                    </div>
                )}

                {selectedTaxon && (
                    <div className="mt-2 p-2 bg-gray-50 rounded border">
                        <p className="font-medium">{selectedTaxon.scientific_name}</p>
                        <p className="text-sm text-gray-600">Level: {selectedTaxon.taxon_rank}</p>
                    </div>
                )}
            </div>

            {/* Textarea untuk alasan penolakan */}
            <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan
                </label>
                <textarea
                    value={disagreeComment}
                    onChange={(e) => setDisagreeComment(e.target.value)}
                    placeholder="Berikan alasan penolakan..."
                    className="w-full p-2 border rounded"
                    rows={4}
                />
            </div>

            <div className="flex justify-end space-x-2">
                <button
                    onClick={() => {
                        if (!selectedTaxon) {
                            alert('Pilih takson terlebih dahulu');
                            return;
                        }
                        if (!disagreeComment.trim()) {
                            alert('Berikan alasan penolakan');
                            return;
                        }
                        handleDisagreeWithIdentification(selectedIdentificationId, disagreeComment);
                        setShowDisagreeModal(false);
                        setDisagreeComment('');
                        setSearchQuery('');
                        setSelectedTaxon(null);
                    }}
                    className="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                >
                    Kirim
                </button>
                <button
                    onClick={() => {
                        setShowDisagreeModal(false);
                        setDisagreeComment('');
                        setSearchQuery('');
                        setSelectedTaxon(null);
                    }}
                    className="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                >
                    Batal
                </button>
            </div>
        </div>
    </div>
)}
        </div>
    );
}

export default TabPanel;
