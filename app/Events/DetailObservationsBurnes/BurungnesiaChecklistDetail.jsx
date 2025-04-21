import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useUser } from '../../../context/UserContext';
import MediaViewer from './MediaViewer';
import ChecklistMap from './ChecklistMap';
import TabPanel from './TabPanel';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faMapMarkerAlt, faFlag } from '@fortawesome/free-solid-svg-icons';
import { apiFetch } from '../../../utils/api';

function BurungnesiaChecklistDetail({ id: propId, isModal = false, onClose = null }) {
    const { id: paramId } = useParams();
    const id = isModal ? propId : paramId;
    const [checklist, setChecklist] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [activeTab, setActiveTab] = useState('identification');
    const [locationName, setLocationName] = useState('Memuat lokasi...');
    const { user } = useUser();

    useEffect(() => {
        fetchChecklistDetail();
        fetchLocationName();
    }, [id]);

    const fetchChecklistDetail = async () => {
        try {
            const response = await apiFetch(`/burungnesia/observations/${id}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });

            const data = await response.json();
            if (data.success) {
                setChecklist(data.data);
            } else {
                setError(data.message);
            }
        } catch (error) {
            setError('Terjadi kesalahan saat mengambil data');
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchLocationName = async () => {
        if (checklist?.latitude && checklist?.longitude) {
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${checklist.latitude}&lon=${checklist.longitude}`
                );
                const data = await response.json();
                setLocationName(data.display_name);
            } catch (error) {
                console.error('Error fetching location name:', error);
                setLocationName('Lokasi tidak dapat dimuat');
            }
       }
   };
    if (loading) {
       return (
           <div className="flex items-center justify-center min-h-screen">
               <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
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
       <div className={`${isModal ? '' : 'container mx-auto mt-10'} px-4 py-8`}>
           {/* Header Section */}
           <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
               <div className="flex justify-between items-start">
                   <div>
                       <h1 className="text-2xl font-bold mb-2">
                           {checklist?.scientific_name || 'Nama tidak tersedia'}
                       </h1>
                       <div className="text-gray-600">
                           Pengamat: {checklist?.observer_name || 'Tidak diketahui'} pada{' '}
                           {checklist?.created_at ? new Date(checklist.created_at).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}
                       </div>
                   </div>
                   <div className={`px-4 py-2 rounded-full text-sm font-semibold
                       ${checklist?.quality_grade === 'research grade' ? 'bg-green-100 text-green-800' :
                       checklist?.quality_grade === 'needs ID' ? 'bg-yellow-100 text-yellow-800' :
                       'bg-gray-100 text-gray-800'}`}>
                       {checklist?.quality_grade === 'research grade' ? 'ID Lengkap' :
                        checklist?.quality_grade === 'needs ID' ? 'Butuh ID' : 'Casual'}
                   </div>
               </div>
           </div>
            {/* Media and Map Section */}
           <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
               <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                   <div>
                       <h2 className="text-xl font-semibold mb-4">Media</h2>
                       <MediaViewer checklist={checklist} />
                   </div>
                   <div>
                       <h2 className="text-xl font-semibold mb-4">Peta Sebaran</h2>
                       <ChecklistMap checklist={checklist} />
                       <div className="text-sm text-gray-500 mt-2 text-center">
                           <p><FontAwesomeIcon icon={faMapMarkerAlt} className="text-red-500" /> {locationName}</p>
                       </div>
                   </div>
               </div>
           </div>
            {/* Tab Panel Section */}
           <TabPanel
               id={id}
               activeTab={activeTab}
               setActiveTab={setActiveTab}
               checklist={checklist}
               user={user}
           />
       </div>
   );
}

export default BurungnesiaChecklistDetail;
