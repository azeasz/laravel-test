import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from '../../utils/api';
import EditChecklistModal from './EditChecklistModal';
import { PencilIcon, TrashIcon } from '@heroicons/react/24/outline';
import MediaViewer from './MediaViewer';
import ChecklistMapBurkup from './ChecklistMapBurkup';
import TaxonomyDetail from './TaxonomyDetail';
import LocationDetail from './LocationDetail';

function AppChecklistDetail() {
    const { id } = useParams();
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [locationName, setLocationName] = useState('Memuat lokasi...');
    const queryClient = useQueryClient();

    // Tentukan source dan endpoint berdasarkan prefix ID
    const source = id.startsWith('BN') ? 'burungnesia' :
                  id.startsWith('KP') ? 'kupunesia' : null;

    const endpoint = (() => {
        if (source === 'burungnesia') {
            return `/burungnesia/checklists/${id.substring(2)}`;
        } else if (source === 'kupunesia') {
            return `/kupunesia/checklists/${id.substring(2)}`;
        }
        return null;
    })();

    // Query untuk mengambil data checklist
    const { data: checklistData, isLoading, error } = useQuery({
        queryKey: ['checklist', id],
        queryFn: async () => {
            if (!endpoint) {
                throw new Error('ID checklist tidak valid');
            }
            const response = await apiFetch(endpoint);
            if (!response.ok) {
                throw new Error('Gagal memuat data checklist');
            }
            return response.json();
        },
        enabled: !!endpoint
    });

    // Fungsi untuk mendapatkan nama lokasi
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
        if (checklistData?.data?.checklist?.latitude && checklistData?.data?.checklist?.longitude) {
            getLocationName(
                checklistData.data.checklist.latitude,
                checklistData.data.checklist.longitude
            ).then(name => setLocationName(name));
        }
    }, [checklistData]);

    const handleUpdateSuccess = async () => {
        await queryClient.invalidateQueries(['checklist', id]);
        setShowEditModal(false);
    };

    if (!source) {
        return <div className="text-red-500 text-center p-4">Error: Format ID tidak valid</div>;
    }

    if (isLoading) {
        return <div className="text-center p-4">Memuat...</div>;
    }

    if (error) {
        return <div className="text-red-500 text-center p-4">Error: {error.message}</div>;
    }

    const { checklist, fauna, media } = checklistData.data;

    return (
        <div className="container mx-auto px-4 py-8">
            <div className="bg-white rounded-lg shadow p-6 mb-6">
                {/* Header */}
                <div className="flex justify-between items-center mb-4">
                    <h1 className="text-2xl font-bold">Detail Checklist</h1>
                    {checklist.can_edit && (
                        <div className="flex gap-2">
                            <button
                                onClick={() => setShowEditModal(true)}
                                className="p-2 text-blue-600 hover:bg-blue-50 rounded-full"
                                title="Edit Checklist"
                            >
                                <PencilIcon className="w-5 h-5" />
                            </button>
                            <button
                                onClick={() => setShowDeleteModal(true)}
                                className="p-2 text-red-600 hover:bg-red-50 rounded-full"
                                title="Hapus Checklist"
                            >
                                <TrashIcon className="w-5 h-5" />
                            </button>
                        </div>
                    )}
                </div>

                {/* Map Section */}
                <div className="mb-6">
                    <ChecklistMapBurkup
                        latitude={checklist.latitude}
                        longitude={checklist.longitude}
                        locationName={locationName}
                    />
                    <p className="text-sm text-gray-500 mt-2 text-center">
                        {locationName}
                    </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Informasi Dasar */}
                    <div>
                        <h2 className="text-lg font-semibold mb-2">Informasi Dasar</h2>
                        <div className="space-y-2">
                            <p><span className="font-medium">Observer:</span> {checklist.observer}</p>
                            <p><span className="font-medium">Username:</span> {checklist.username}</p>
                            <p><span className="font-medium">Tanggal:</span> {checklist.tgl_pengamatan}</p>
                            <p><span className="font-medium">Waktu:</span> {checklist.start_time} - {checklist.end_time}</p>
                            <p><span className="font-medium">Koordinat:</span> {checklist.latitude}, {checklist.longitude}</p>
                            <p><span className="font-medium">Total Observasi:</span> {checklist.total_observations}</p>
                        </div>
                    </div>

                    {/* Daftar Fauna */}
                    <div>
                        <h2 className="text-lg font-semibold mb-2">Daftar Fauna</h2>
                        <div className="space-y-4">
                            {fauna.map((f) => (
                                <div key={f.id} className="border p-3 rounded">
                                    <p className="font-medium">{f.nama_lokal}</p>
                                    <p className="text-gray-600 italic">{f.nama_ilmiah}</p>
                                    <p className="text-sm text-gray-500">Family: {f.family}</p>
                                    <p>Jumlah: {f.jumlah}</p>
                                    <p>Catatan: {f.catatan || '-'}</p>
                                    {f.breeding && (
                                        <div className="mt-2">
                                            <p className="text-green-600">Breeding</p>
                                            <p>{f.breeding_note || '-'}</p>
                                            {source === 'kupunesia' && f.breeding_type_name && (
                                                <p className="text-sm">Tipe: {f.breeding_type_name}</p>
                                            )}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Catatan Tambahan */}
                {checklist.additional_note && (
                    <div className="mt-6">
                        <h2 className="text-lg font-semibold mb-2">Catatan Tambahan</h2>
                        <p className="text-gray-700">{checklist.additional_note}</p>
                    </div>
                )}

                {/* Media Section */}
                {media.images.length > 0 && (
                    <div className="mt-6">
                        <h2 className="text-lg font-semibold mb-2">Foto</h2>
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            {media.images.map((img) => (
                                <div key={img.id} className="relative">
                                    <img
                                        src={img.url}
                                        alt="Foto pengamatan"
                                        className="w-full h-48 object-cover rounded"
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* Modal Edit */}
            {showEditModal && checklist.can_edit && (
                <EditChecklistModal
                    checklist={checklist}
                    fauna={fauna}
                    onClose={() => setShowEditModal(false)}
                    onSuccess={handleUpdateSuccess}
                    source={source}
                />
            )}
        </div>
    );
}

export default AppChecklistDetail;
