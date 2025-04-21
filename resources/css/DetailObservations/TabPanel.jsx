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
    handleLocationVerify,
    handleWildStatusVote,
    locationVerifications,
    wildStatusVotes,
    handleAgreeWithIdentification
}) {
    const [searchQuery, setSearchQuery] = useState('');
    const [photo, setPhoto] = useState(null);

    const tabs = [
        { id: 'identification', label: 'Identifikasi', icon: faSearch },
        { id: 'comments', label: 'Komentar', icon: faComments },
        { id: 'location', label: 'Verifikasi Lokasi', icon: faMapMarkerAlt },
        { id: 'wild', label: 'Status Liar', icon: faPaw }
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

    const handleSubmit = (e) => {
        e.preventDefault();
        handleIdentificationSubmit(photo);
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
                        <span className="text-sm text-gray-500">Bantu Sikebo memastikan identifikasinya,
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
                            <form onSubmit={handleSubmit} className="space-y-4">
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

                                <div className="mb-4">
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Unggah Foto (opsional)
                                    </label>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) => setPhoto(e.target.files[0])}
                                        className="w-full p-2 border rounded"
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
                            {identifications.map(identification => (
                                <div key={identification.id} className="border-b py-4">

                                    <div className="flex justify-between items-start">
                                        <div>
                                            <p className="font-medium">{identification.scientific_name}</p>
                                            <span>Level: {identification.identification_level}</span>
                                            <p className="text-sm text-gray-600">
                                                oleh {identification.uname} pada{' '}
                                                {new Date(identification.created_at).toLocaleDateString('id-ID')}
                                            </p>
                                            {identification.comment && (
                                                <p className="mt-2 text-gray-700">{identification.comment}</p>
                                            )}
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <span className="text-sm text-gray-500">
                                                {identification.agreement_count || 0} setuju
                                            </span>
                                            <button
                                                onClick={() => handleAgreeWithIdentification(identification.id)}
                                                className={`px-3 py-1 rounded ${
                                                    identification.user_agreed
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                                                }`}
                                            >
                                                {identification.user_agreed ? 'Disetujui' : 'Setuju'}
                                            </button>
                                        </div>
                                    </div>
                                    <span className="text-sm text-gray-500"> Apakah identifikasi di atas sudah benar?</span>
                                </div>
                            ))}
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

                {activeTab === 'location' && (
                    <div>
                        <div className="flex space-x-4 mb-6">
                            <button
                                onClick={() => handleLocationVerify(true)}
                                className="flex-1 bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600"
                            >
                                Lokasi Akurat
                            </button>
                            <button
                                onClick={() => handleLocationVerify(false)}
                                className="flex-1 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600"
                            >
                                Lokasi Tidak Akurat
                            </button>
                        </div>

                        <div className="space-y-4">
                            {locationVerifications.map(verification => (
                                <div key={verification.id} className="border-b pb-4">
                                    <div className="flex items-center justify-between">
                                        <span className="font-medium">{verification.user_name}</span>
                                        <span className={`px-2 py-1 rounded ${
                                            verification.is_accurate ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        }`}>
                                            {verification.is_accurate ? 'Akurat' : 'Tidak Akurat'}
                                        </span>
                                    </div>
                                    {verification.comment && (
                                        <p className="mt-2 text-gray-600">{verification.comment}</p>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {activeTab === 'wild' && (
                    <div>
                        <div className="flex space-x-4 mb-6">
                            <button
                                onClick={() => handleWildStatusVote(true)}
                                className="flex-1 bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600"
                            >
                                Organisme Liar
                            </button>
                            <button
                                onClick={() => handleWildStatusVote(false)}
                                className="flex-1 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600"
                            >
                                Bukan Organisme Liar
                            </button>
                        </div>

                        <div className="space-y-4">
                            {wildStatusVotes.map(vote => (
                                <div key={vote.id} className="border-b pb-4">
                                    <div className="flex items-center justify-between">
                                        <span className="font-medium">{vote.user_name}</span>
                                        <span className={`px-2 py-1 rounded ${
                                            vote.is_wild ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        }`}>
                                            {vote.is_wild ? 'Liar' : 'Bukan Liar'}
                                        </span>
                                    </div>
                                    {vote.comment && (
                                        <p className="mt-2 text-gray-600">{vote.comment}</p>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default TabPanel;
