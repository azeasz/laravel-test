import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../../utils/api';
import Map from './Map';
import MediaViewer from './MediaViewer';
import TaxonomyDetails from './TaxonomyDetails';

const ChecklistDetailBurkup = () => {
    const { id } = useParams();
    const [locationName, setLocationName] = useState('Memuat lokasi...');

    const { data, isLoading, error } = useQuery({
        queryKey: ['checklist-detail', id],
        queryFn: async () => {
            const source = id.startsWith('BN') ? 'burungnesia' : 'kupunesia';
            const response = await apiFetch(`/observations/${id}?source=${source}`);
            return response.json();
        }
    });

    useEffect(() => {
        const fetchLocationName = async () => {
            if (data?.data?.checklist?.latitude && data?.data?.checklist?.longitude) {
                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${data.data.checklist.latitude}&lon=${data.data.checklist.longitude}&zoom=18&addressdetails=1`
                    );
                    const locationData = await response.json();
                    setLocationName(locationData.display_name);
                } catch (error) {
                    console.error('Error fetching location name:', error);
                    setLocationName('Lokasi tidak dapat dimuat');
                }
            }
        };

        fetchLocationName();
    }, [data]);

    if (isLoading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-lg text-gray-600">Memuat...</div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-lg text-red-600">Error: {error.message}</div>
            </div>
        );
    }

    if (!data?.data?.checklist) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-lg text-red-600">Data tidak tersedia</div>
            </div>
        );
    }

    const { checklist, media } = data.data;

    return (
        <div className="container mx-auto px-4 py-8">
            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div className="flex justify-between items-start">
                    <div>
                        <h1 className="text-2xl font-bold mb-2">
                            {checklist?.fauna?.nama_lokal || 'Nama tidak tersedia'}
                        </h1>
                        <h2 className="text-xl text-gray-600 italic mb-2">
                            {checklist?.fauna?.nama_ilmiah || '-'}
                        </h2>
                        <div className="text-gray-600">
                            Pengamat: {checklist?.observer || 'Tidak diketahui'} pada{' '}
                            {checklist?.tgl_pengamatan ?
                                new Date(checklist.tgl_pengamatan).toLocaleDateString('id-ID') :
                                'Tanggal tidak tersedia'
                            }
                        </div>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 className="text-xl font-semibold mb-4">Media</h2>
                    <MediaViewer
                        images={media?.images || []}
                        sounds={media?.sounds || []}
                    />
                </div>
                <div className="space-y-6">
                    <div>
                        <h2 className="text-xl font-semibold mb-4">Lokasi</h2>
                        <Map
                            latitude={checklist?.latitude}
                            longitude={checklist?.longitude}
                            locationName={locationName}
                        />
                        <div className="text-sm text-gray-500 mt-2 text-center">
                            {locationName}
                        </div>
                    </div>
                    <TaxonomyDetails fauna={checklist?.fauna || {}} />
                </div>
            </div>
        </div>
    );
};

export default ChecklistDetailBurkup;
