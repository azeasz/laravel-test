import React, { useState } from 'react';
import LocationPicker from './Observations/LocationPicker';
import Modal from './Observations/LPModal';
import LocationInput from './Observations/LocationInput';
import { useUser } from '../context/UserContext';
import { useNavigate } from 'react-router-dom';

function FobiUpload() {
    const { user } = useUser();
    const navigate = useNavigate();
    
    const [faunaName, setFaunaName] = useState('');
    const [faunaId, setFaunaId] = useState('');
    const [formData, setFormData] = useState({
        latitude: '',
        longitude: '',
        tujuan_pengamatan: 0,
        observer: '',
        additional_note: '',
        active: 0,
        tgl_pengamatan: '',
        start_time: '',
        end_time: '',
        completed: 0,
        count: '',
        notes: '',
        breeding: false,
        breeding_note: '',
        breeding_type_id: '',
        images: [],
        sounds: []
    });
 // State untuk loading dan error
 const [loading, setLoading] = useState(false);
 const [progress, setProgress] = useState(0);
 const [loadingMessage, setLoadingMessage] = useState('');
 const [isSearching, setIsSearching] = useState(false);
 const [error, setError] = useState('');
 
 // State untuk modal
 const [isModalOpen, setIsModalOpen] = useState(false);
 const [isFileModalOpen, setIsFileModalOpen] = useState(false);
 const [isLocationModalOpen, setIsLocationModalOpen] = useState(false);
 const [isConfirmModalOpen, setIsConfirmModalOpen] = useState(false);
 
 // State untuk data
 const [locationName, setLocationName] = useState('');
 const [birdList, setBirdList] = useState([]);
 const [editIndex, setEditIndex] = useState(null);

 const [suggestions, setSuggestions] = useState([]);
const [showSuggestions, setShowSuggestions] = useState(false);

 // Fungsi simulasi progress
 const simulateProgress = (duration = 1000, message = 'Memproses...') => {
     setLoading(true);
     setLoadingMessage(message);
     setProgress(0);
     
     const interval = setInterval(() => {
         setProgress(prev => {
             if (prev >= 100) {
                 clearInterval(interval);
                 return 100;
             }
             return prev + 10;
         });
     }, duration / 10);

     return () => clearInterval(interval);
 };

 const handleFaunaNameChange = async (name) => {
    setFaunaName(name);
    // Reset fauna_id ketika input berubah
    setFaunaId('');
    
    if (name.length > 2) {
        setIsSearching(true);
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/faunas?name=${encodeURIComponent(name)}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                }
            });
            
            if (!response.ok) throw new Error('Gagal mengambil data fauna');
            
            const data = await response.json();
            if (data.success) {
                setSuggestions(data.data);
                setShowSuggestions(true);
            }
        } catch (error) {
            console.error('Error:', error);
            setError('Gagal mengambil data fauna');
        } finally {
            setIsSearching(false);
        }
    } else {
        setSuggestions([]);
        setShowSuggestions(false);
    }
};

const handleSelectFauna = (fauna) => {
    setFaunaName(fauna.nameId); // Hanya tampilkan nama Indonesia
    setFaunaId(parseInt(fauna.id)); // Pastikan id disimpan sebagai integer
    setShowSuggestions(false);
};
const handleInputChange = (e) => {
     const { name, value, type, checked } = e.target;
     setFormData(prevFormData => ({
         ...prevFormData,
         [name]: type === 'checkbox' ? checked : value
     }));
 };

 const handleLocationSave = (lat, lng, name) => {
     setFormData(prevFormData => ({
         ...prevFormData,
         latitude: lat,
         longitude: lng
     }));
     setLocationName(name);
     setIsLocationModalOpen(false);
 };

 const handleFileChange = async (e) => {
     const { files } = e.target;
     setLoading(true);
     setLoadingMessage('Memproses file...');
     setProgress(0);

     const totalFiles = files.length;
     let processedFiles = 0;
     let newImages = [];
     let newSounds = [];

     for (const file of Array.from(files)) {
         if (file.type.startsWith('image/')) {
             newImages.push(file);
         } else if (file.type.startsWith('audio/')) {
             newSounds.push(file);
         }
         processedFiles++;
         setProgress((processedFiles / totalFiles) * 100);
     }

     setFormData(prevFormData => ({
         ...prevFormData,
         images: [...prevFormData.images, ...newImages],
         sounds: [...prevFormData.sounds, ...newSounds]
     }));

     setLoading(false);
 };
 
 const resetMediaData = () => {
        setFormData(prevFormData => ({
            ...prevFormData,
            images: [],
            sounds: [],
            spectrogramUrl: []
        }));
    };

    const handleAddBird = () => {
        const newBird = {
            ...formData,
            faunaName,
            faunaId: parseInt(faunaId, 10), // Pastikan faunaId adalah integer
            spectrogramUrl: null
        };
        if (editIndex !== null) {
            const updatedBirdList = birdList.map((bird, index) =>
                index === editIndex ? newBird : bird
            );
            setBirdList(updatedBirdList);
            setEditIndex(null);
        } else {
            setBirdList(prevBirdList => [...prevBirdList, newBird]);
        }
        setIsModalOpen(false);
        resetMediaData();
    };

    const handleEditBird = async (index) => {
        const bird = birdList[index];
        setFaunaName(bird.faunaName);

        try {
            const response = await fetch(`http://127.0.0.1:8000/api/faunas?name=${encodeURIComponent(bird.faunaName)}`);
            if (!response.ok) {
                throw new Error('Failed to fetch fauna ID');
            }
            const data = await response.json();
            if (data.fauna_id) {
                setFaunaId(data.fauna_id);
            } else {
                setFaunaId('');
            }
        } catch (error) {
            console.error('Error fetching fauna ID:', error);
            setError('Error fetching fauna ID');
            setFaunaId('');
        }

        setFormData({
            ...formData,
            ...bird,
            images: editIndex === index ? formData.images : bird.images || [],
            sounds: editIndex === index ? formData.sounds : bird.sounds || [],
            spectrogramUrl: bird.spectrogramUrl || null
        });
        setEditIndex(index);
        setIsModalOpen(true);
    };

    const handleDeleteBird = (index) => {
        const newList = birdList.filter((_, i) => i !== index);
        setBirdList(newList);
    };

    const handleOpenModal = () => {
        resetMediaData();
        setIsModalOpen(true);
    };

    const handleFileModalOpen = (index) => {
        setEditIndex(index);
        setIsFileModalOpen(true);
    };

    const handleFileModalClose = () => {
        setIsFileModalOpen(false);
        setEditIndex(null);
    };

    const handleFileSave = async () => {
        try {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                throw new Error('Token tidak ditemukan');
            }
            
            setLoading(true);
            setLoadingMessage('Menyimpan file dan membuat spektrogram...');
            setProgress(0);
            
            const fileData = new FormData();
            
            // Proses upload file
            formData.images.forEach((image) => {
                fileData.append('images[]', image);
            });
            formData.sounds.forEach((sound) => {
                fileData.append('sounds[]', sound);
            });
    
            let spectrogramUrl = null;
    
            if (formData.sounds.length > 0) {
                const response = await fetch('http://127.0.0.1:8000/api/generate-spectrogram', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    body: fileData
                });
    
                if (response.status === 401) {
                    navigate('/login', { replace: true });
                    return;
                }
    
                if (!response.ok) {
                    throw new Error('Gagal membuat spektrogram');
                }
    
                const data = await response.json();
                spectrogramUrl = data.spectrogramUrl;
            }
    
            // Set progress ke 100% setelah berhasil
            setProgress(100);
    
            // Update bird list
            const updatedBirdList = birdList.map((bird, index) =>
                index === editIndex ? {
                    ...bird,
                    images: [...bird.images, ...formData.images],
                    sounds: [...bird.sounds, ...formData.sounds],
                    spectrogramUrl: spectrogramUrl || bird.spectrogramUrl
                } : bird
            );
            setBirdList(updatedBirdList);
    
            // Reset form data
            setFormData(prevFormData => ({
                ...prevFormData,
                images: [],
                sounds: []
            }));
    
            // Tunggu sebentar untuk menampilkan 100%
            await new Promise(resolve => setTimeout(resolve, 500));
    
        } catch (error) {
            console.error('Error:', error);
            setError(error.message);
            if (error.message.includes('Token')) {
                navigate('/login', { replace: true });
            }
        } finally {
            // Pastikan loading dan progress dibersihkan
            setTimeout(() => {
                setLoading(false);
                setProgress(0);
                handleFileModalClose();
            }, 500);
        }
    };
    
    const handleRemoveMediaFromList = (birdIndex, mediaType, mediaIndex) => {
        setBirdList(prevList => {
            const updatedList = [...prevList];
            updatedList[birdIndex] = {
                ...updatedList[birdIndex],
                [mediaType]: updatedList[birdIndex][mediaType].filter((_, i) => i !== mediaIndex)
            };
            return updatedList;
        });
    };

    const handleConfirmSubmit = (e) => {
        e.preventDefault();
        setIsConfirmModalOpen(true);
    };

    const handleSubmit = async () => {
        setError('');
        setIsConfirmModalOpen(false);
        setLoading(true);
        setProgress(0);
        
        try {
            // Mulai progress
            const progressInterval = setInterval(() => {
                setProgress(prev => {
                    if (prev >= 90) return 90;
                    return prev + 10;
                });
            }, 500);
    
            // Validasi dan fetch ulang fauna_id untuk setiap burung
            const validatedBirdList = await Promise.all(birdList.map(async (bird) => {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/faunas?name=${encodeURIComponent(bird.faunaName)}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                        }
                    });
                    
                    if (!response.ok) throw new Error('Gagal memvalidasi data fauna');
                    
                    const data = await response.json();
                    if (!data.success || !data.data.length) {
                        throw new Error(`Fauna "${bird.faunaName}" tidak ditemukan`);
                    }
    
                    // Cari fauna yang cocok dengan nama yang ada
                    const matchedFauna = data.data.find(f => f.nameId === bird.faunaName);
                    if (!matchedFauna) {
                        throw new Error(`Data fauna "${bird.faunaName}" tidak valid`);
                    }
    
                    // Update fauna_id dengan data terbaru
                    return {
                        ...bird,
                        faunaId: parseInt(matchedFauna.id)
                    };
                } catch (error) {
                    throw new Error(`Error validasi fauna "${bird.faunaName}": ${error.message}`);
                }
            }));
    
            const formDataToSend = new FormData();
            // Append semua data form
            Object.keys(formData).forEach(key => {
                if (key !== 'images' && key !== 'sounds') {
                    formDataToSend.append(key, formData[key]);
                }
            });
    
            // Append data burung yang sudah divalidasi
            validatedBirdList.forEach((bird, index) => {
                formDataToSend.append(`fauna_id[${index}]`, bird.faunaId);
                formDataToSend.append(`count[${index}]`, bird.count);
                formDataToSend.append(`notes[${index}]`, bird.notes);
                formDataToSend.append(`breeding[${index}]`, bird.breeding ? '1' : '0');
                formDataToSend.append(`breeding_note[${index}]`, bird.breeding_note);
                formDataToSend.append(`breeding_type_id[${index}]`, bird.breeding_type_id);
                
                bird.images.forEach((image) => {
                    formDataToSend.append(`images[${index}][]`, image);
                });
                bird.sounds.forEach((sound) => {
                    formDataToSend.append(`sounds[${index}][]`, sound);
                });
            });
    
            const response = await fetch('http://127.0.0.1:8000/api/checklist-fauna', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: formDataToSend
            });
    
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Gagal mengunggah data');
            }
    
            const data = await response.json();
            
            setProgress(100);
            clearInterval(progressInterval);
    
            if (data.success) {
                await new Promise(resolve => setTimeout(resolve, 500));
                
                alert('Data berhasil diunggah!');
                // Reset form
                setFormData({
                    latitude: '',
                    longitude: '',
                    tujuan_pengamatan: 0,
                    observer: '',
                    additional_note: '',
                    active: 0,
                    tgl_pengamatan: '',
                    start_time: '',
                    end_time: '',
                    completed: 0,
                    count: '',
                    notes: '',
                    breeding: false,
                    breeding_note: '',
                    breeding_type_id: '',
                    images: [],
                    sounds: []
                });
                setBirdList([]);
            }
        } catch (error) {
            console.error('Error:', error);
            setError(error.message);
        } finally {
            setTimeout(() => {
                setLoading(false);
                setProgress(0);
            }, 500);
        }
    };    
    return (
        <div className="p-4 mt-12 md:mt-16">
{loading && (
            <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                <div className="bg-white p-6 rounded-lg shadow-xl w-96">
                    <h3 className="text-lg font-semibold mb-4">{loadingMessage}</h3>
                    <div className="w-full bg-gray-200 rounded-full h-4 mb-2">
                        <div
                            className="bg-blue-600 h-4 rounded-full transition-all duration-300"
                            style={{ width: `${progress}%` }}
                        ></div>
                    </div>
                    <p className="text-center">{Math.round(progress)}%</p>
                </div>
            </div>
        )}

            {/* Error Message */}
            {error && (
                <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span className="block sm:inline">{error}</span>
                    <span className="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg onClick={() => setError('')} className="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            )}
<form onSubmit={handleConfirmSubmit} className="space-y-6">
    <h2 className="text-xl font-semibold">Checklist Burungnesia</h2>
    
    {/* Lokasi */}
    <div className="bg-gray-50 p-4 rounded-lg">
        <h3 className="text-lg font-medium mb-3">Lokasi Pengamatan</h3>
        <LocationInput
            locationName={locationName}
            latitude={formData.latitude}
            longitude={formData.longitude}
            onTrigger={() => setIsLocationModalOpen(true)}
        />
    </div>

    {/* Waktu Pengamatan */}
    <div className="bg-gray-50 p-4 rounded-lg">
        <h3 className="text-lg font-medium mb-3">Waktu Pengamatan</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input 
                type="date" 
                name="tgl_pengamatan" 
                placeholder="Tanggal Pengamatan" 
                className="border p-2 rounded" 
                onChange={handleInputChange} 
                value={formData.tgl_pengamatan} 
            />
            <input 
                type="time" 
                name="start_time" 
                placeholder="Waktu Mulai" 
                className="border p-2 rounded" 
                onChange={handleInputChange} 
                value={formData.start_time} 
            />
            <input 
                type="time" 
                name="end_time" 
                placeholder="Waktu Selesai" 
                className="border p-2 rounded" 
                onChange={handleInputChange} 
                value={formData.end_time} 
            />
        </div>
    </div>

    {/* Data Pengamat */}
    <div className="bg-gray-50 p-4 rounded-lg">
        <h3 className="text-lg font-medium mb-3">Data Pengamat</h3>
        <div className="grid grid-cols-1 gap-4">
            <input 
                type="text" 
                name="observer" 
                placeholder="Nama Pengamat" 
                required 
                className="border p-2 w-full rounded" 
                onChange={handleInputChange} 
                value={formData.observer} 
            />
            <select 
                name="tujuan_pengamatan"
                className="border p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                value={formData.tujuan_pengamatan}
                onChange={handleInputChange}
                required
            >
                <option value="">Pilih Tujuan Pengamatan</option>
                <option value="1">Terencana/Terjadwal (Survey, Inventarisasi, Pengamatan Rutin, dll)</option>
                <option value="2">Insidental/tidak ditujukan untuk pengamatan</option>
            </select>
            <textarea 
                name="additional_note" 
                placeholder="Catatan Tambahan" 
                className="border p-2 w-full rounded" 
                onChange={handleInputChange} 
                value={formData.additional_note} 
                rows="3"
            />
        </div>
    </div>

    {/* Status Pengamatan */}
    <div className="bg-gray-50 p-4 rounded-lg">
        <h3 className="text-lg font-medium mb-3">Status Pengamatan</h3>
        <div className="flex space-x-6">
            <div className="flex items-center">
                <input 
                    type="checkbox" 
                    name="active" 
                    id="active"
                    className="w-4 h-4 mr-2" 
                    checked={formData.active === 1}
                    onChange={(e) => {
                        setFormData(prev => ({
                            ...prev,
                            active: e.target.checked ? 1 : 0
                        }));
                    }}
                />
                <label htmlFor="active" className="text-gray-700">Aktifitas</label>
            </div>
            <div className="flex items-center">
                <input 
                    type="checkbox" 
                    name="completed" 
                    id="completed"
                    className="w-4 h-4 mr-2" 
                    checked={formData.completed === 1}
                    onChange={(e) => {
                        setFormData(prev => ({
                            ...prev,
                            completed: e.target.checked ? 1 : 0
                        }));
                    }}
                />
                <label htmlFor="completed">Checklist Lengkap</label>
            </div>
        </div>
    </div>

    {/* Tombol Aksi */}
    <div className="flex space-x-4">
        <button 
            type="button" 
            onClick={() => setIsModalOpen(true)} 
            className="bg-[#679995] hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center"
        >
            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Jenis
        </button>
        <button 
            type="submit" 
            className="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg"
        >
            Upload Data
        </button>
    </div>
</form>
            <Modal isOpen={isLocationModalOpen} onClose={() => setIsLocationModalOpen(false)}>
                <LocationPicker onSave={handleLocationSave} />
            </Modal>
            {error && <p className="text-red-500 mt-4">{error}</p>}

            {isConfirmModalOpen && (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-700">
        <div className="bg-white p-6 rounded shadow-lg w-96">
            <h2 className="text-xl font-semibold mb-4">Apakah data sudah benar?</h2>
            <p><strong>Tujuan Pengamatan:</strong> {formData.tujuan_pengamatan}</p>
            <p><strong>Observer:</strong> {formData.observer}</p>
            <p><strong>Catatan Tambahan:</strong> {formData.additional_note}</p>
            <p><strong>Latitude:</strong> {formData.latitude}</p>
            <p><strong>Longitude:</strong> {formData.longitude}</p>
            <p><strong>Jumlah Total Burung:</strong> {birdList.length}</p>
            <div>
                <h3 className="text-lg font-semibold mt-4">Detail Burung:</h3>
                {birdList.map((bird, index) => (
                    <div key={index} className="mt-2">
                        <p><strong>Nama:</strong> {bird.faunaName}</p>
                        <p><strong>Jumlah:</strong> {bird.count}</p>
                        <p><strong>Catatan:</strong> {bird.notes}</p>
                        <p><strong>Berbiak:</strong> {bird.breeding ? 'Ya' : 'Tidak'}</p>
                    </div>
                ))}
            </div>
            <div className="flex justify-between mt-4">
                <button onClick={handleSubmit} className="bg-green-500 text-white p-2 rounded">Ya, Kirim</button>
                <button onClick={() => setIsConfirmModalOpen(false)} className="bg-red-500 text-white p-2 rounded">Batal</button>
            </div>
        </div>
    </div>
)}
{birdList.length > 0 && (
    <div className="mt-8">
        <div className="bg-white p-6 rounded-lg shadow-md">
            <h2 className="text-xl font-semibold mb-6">Daftar Burung</h2>
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Media</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berbiak</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {birdList.map((bird, birdIndex) => (
                            <tr key={birdIndex} className="hover:bg-gray-50 transition-colors">
                                <td className="px-6 py-4">
{/* Tampilan Media */}
<div className="space-y-4">
    {/* Tampilan Gambar */}
    {bird.images && bird.images.length > 0 && (
        <div>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                {bird.images.map((image, imageIndex) => (
                    <div key={imageIndex} className="relative group aspect-square max-w-[300px] w-full mx-auto">
                        <img 
                            src={URL.createObjectURL(image)} 
                            alt="Foto Burung" 
                            className="w-full h-full object-cover rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                        />
                        <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg" />
                        <button 
                            onClick={() => handleRemoveMediaFromList(birdIndex, 'images', imageIndex)}
                            className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-red-600 shadow-lg"
                        >
                            ×
                        </button>
                    </div>
                ))}
            </div>
        </div>
    )}
    
    {/* Tampilan Audio */}
    {bird.sounds && bird.sounds.length > 0 && (
        <div>
            <div className="space-y-3">
                {bird.sounds.map((sound, soundIndex) => (
                    <div key={soundIndex} className="relative group bg-gray-50 p-3 rounded-lg">
                        <audio 
                            src={URL.createObjectURL(sound)} 
                            controls 
                            className="w-full h-12 rounded shadow-sm focus:outline-none"
                            controlsList="nodownload noplaybackrate"
                        />
                        <button 
                            onClick={() => handleRemoveMediaFromList(birdIndex, 'sounds', soundIndex)}
                            className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-red-600 shadow-lg"
                        >
                            ×
                        </button>
                    </div>
                ))}
            </div>
        </div>
    )}
</div>                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <div className="text-sm font-medium text-gray-900">{bird.faunaName}</div>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span className="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {bird.count}
                                    </span>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                        bird.breeding 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-gray-100 text-gray-800'
                                    }`}>
                                        {bird.breeding ? 'Ya' : 'Tidak'}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-sm text-gray-500">
                                    {bird.notes}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div className="flex flex-col md:flex-row gap-2">
                                        <button 
                                            onClick={() => handleEditBird(birdIndex)} 
                                            className="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <svg className="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button 
                                            onClick={() => handleDeleteBird(birdIndex)} 
                                            className="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        >
                                            <svg className="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                        <button 
                                            onClick={() => handleFileModalOpen(birdIndex)} 
                                            className="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            <svg className="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Tambah Media
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
)}
{isFileModalOpen && (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center p-4">
        <div className="bg-white p-4 md:p-6 rounded shadow-lg w-full max-w-lg">
            <h2 className="text-xl font-semibold mb-4">Tambah Foto/Audio</h2>
            <input 
                type="file" 
                name="media" 
                onChange={handleFileChange} 
                className="border p-2 w-full mb-2" 
                multiple 
            />
            {/* Preview Media */}
            {formData.images.length > 0 && (
                <div className="mb-4">
                    <h3 className="font-semibold mb-2">Foto:</h3>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                        {formData.images.map((image, index) => (
                            <div key={index} className="relative group">
                                <img 
                                    src={URL.createObjectURL(image)} 
                                    alt={`Preview ${index}`} 
                                    className="w-full h-20 object-cover rounded"
                                />
                                <button 
                                    onClick={() => handleRemoveMedia('images', index)}
                                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600"
                                >
                                    ×
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {formData.sounds.length > 0 && (
                <div className="mb-4">
                    <h3 className="font-semibold mb-2">Audio:</h3>
                    <div className="space-y-2">
                        {formData.sounds.map((sound, index) => (
                            <div key={index} className="relative group">
                                <audio 
                                    src={URL.createObjectURL(sound)} 
                                    controls 
                                    className="w-full"
                                />
                                <button 
                                    onClick={() => handleRemoveMedia('sounds', index)}
                                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600"
                                >
                                    ×
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            <div className="flex flex-col md:flex-row justify-between gap-2">
                <button 
                    onClick={handleFileSave} 
                    className="bg-green-500 text-white p-2 rounded hover:bg-green-600"
                >
                    Simpan
                </button>
                <button 
                    onClick={handleFileModalClose} 
                    className="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                >
                    Batal
                </button>
            </div>
        </div>
    </div>
)}
{isModalOpen && (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div className="bg-white p-6 rounded shadow-lg w-96 relative"> {/* Tambahkan relative di sini */}
            {/* Loading indicator di kanan atas modal */}
            {isSearching && (
                <div className="absolute top-2 right-2 z-[700]">
                    <div className="bg-white p-2 rounded-lg shadow-md flex items-center space-x-2">
                        <div className="animate-spin rounded-full h-4 w-4 border-2 border-[#696969] border-t-transparent"></div>
                        <span className="text-sm">Mencari...</span>
                    </div>
                </div>
            )}
            
            <h2 className="text-xl font-semibold mb-4">Tambah Jenis Burung</h2>
            <div className="relative"> 
                <input 
                    type="text" 
                    id="fauna_name" 
                    placeholder="Jenis burung" 
                    required 
                    className="border p-2 w-full mb-2" 
                    value={faunaName} 
                    onChange={(e) => handleFaunaNameChange(e.target.value)} 
                    autoComplete="off" // Tambahkan ini untuk mencegah autocomplete browser
                />
                {showSuggestions && suggestions.length > 0 && (
                    <div className="absolute z-50 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                        {suggestions.map((fauna) => (
                            <div
                                key={fauna.id}
                                className="px-4 py-2 hover:bg-gray-100 cursor-pointer flex flex-col"
                                onClick={() => handleSelectFauna(fauna)}
                            >
                                <span className="font-medium">{fauna.nameId}</span>
                                <span className="text-sm text-gray-600">{fauna.nameLat}</span>
                            </div>
                        ))}
                    </div>
                )}
            </div>
            
            <input type="hidden" name="fauna_id" id="fauna_id" value={faunaId} />
            {!faunaId && faunaName && (
                <p className="text-red-500 text-sm mb-2">
                    Silakan pilih nama burung dari daftar yang tersedia
                </p>
            )}
            
            <input 
                type="text" 
                name="count" 
                placeholder="Jumlah individu" 
                required 
                className="border p-2 w-full mb-2" 
                value={formData.count} 
                onChange={handleInputChange} 
            />
            <input 
                type="text" 
                name="notes" 
                placeholder="Catatan" 
                className="border p-2 w-full mb-2" 
                value={formData.notes} 
                onChange={handleInputChange} 
            />
            <div className="flex items-center mb-4">
                <input 
                    type="checkbox" 
                    name="breeding" 
                    className="mr-2" 
                    checked={formData.breeding} 
                    onChange={handleInputChange} 
                />
                <label htmlFor="breeding">Apakah berbiak?</label>
            </div>
            <div className="flex justify-between">
                <button 
                    onClick={handleAddBird} 
                    className="bg-green-500 text-white p-2 rounded"
                >
                    Simpan
                </button>
                <button 
                    onClick={() => setIsModalOpen(false)} 
                    className="bg-red-500 text-white p-2 rounded"
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

export default FobiUpload;