import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useUser } from '../context/UserContext';
import LocationPicker from './Observations/LocationPicker';
import Modal from './Observations/LPModal';
import LocationInput from './Observations/LocationInput';
import MediaCard from './MediaCard';
import BulkEditModal from './BulkEditModal';

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

    // Handle file drop/select
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
                            } else {
                                console.error('Gagal membuat spektrogram');
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

            // Tambah jeda sebelum menyelesaikan loading
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
        const newProgress = {};
        selectedCards.forEach(id => {
            newProgress[id] = 0;
        });
        setUploadProgress(newProgress);

        selectedCards.forEach((cardId, index) => {
            simulateProgress(cardId, index);
        });
    };

    const simulateProgress = (cardId, delay = 0) => {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress > 100) progress = 100;
            
            setUploadProgress(prev => ({
                ...prev,
                [cardId]: Math.round(progress)
            }));

            if (progress === 100) {
                clearInterval(interval);
                setTimeout(() => {
                    const observation = observations.find(obs => obs.id === cardId);
                    if (observation) {
                        onUpdate(cardId, bulkFormData);
                    }
                }, 300); // Tambah jeda sebelum update
            }
        }, 200 + delay * 50); // Percepat interval untuk animasi yang lebih halus
    };

    const onUpdate = (id, data) => {
        setObservations(prevObservations => 
            prevObservations.map(obs => 
                obs.id === id ? { ...obs, ...data } : obs
            )
        );
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
        setIsConfirmModalOpen(true);
    };

    const handleSubmit = async () => {
        // Validasi data
        if (!formData.scientific_name || !formData.taxon_rank || !formData.kingdom) {
            setError('Silakan lengkapi data taksonomi');
            return;
        }
        if (!formData.tgl_pengamatan) {
            setError('Silakan isi tanggal pengamatan');
            return;
        }

        setError('');
        setIsConfirmModalOpen(false);
        setLoading(true);
        setProgress(0);
        setLoadingMessage('Mengunggah data...');

        try {
            // Mulai progress simulasi
            const progressInterval = setInterval(() => {
                setProgress(prev => {
                    if (prev >= 90) return 90;
                    return prev + 10;
                });
            }, 500);

            const submitFormData = new FormData();
            
            // Tambah data form
            Object.keys(formData).forEach(key => {
                submitFormData.append(key, formData[key]);
            });

            // Tambah data observasi
            observations.forEach((obs, index) => {
                submitFormData.append(`media[${index}]`, obs.file);
                submitFormData.append('latitude', obs.latitude);
                submitFormData.append('longitude', obs.longitude);
                submitFormData.append('description', obs.description || '');
            });

            const response = await fetch('http://127.0.0.1:8000/api/observations', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: submitFormData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal mengupload observasi');
            }

            const data = await response.json();
            clearInterval(progressInterval);
            setProgress(100);

            if (data.success) {
                await new Promise(resolve => setTimeout(resolve, 500));
                alert('Observasi berhasil disimpan!');
                // Reset form
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
            }

        } catch (error) {
            setError(error.message);
            alert(error.message);
        } finally {
            setTimeout(() => {
                setLoading(false);
                setProgress(0);
                setLoadingMessage('');
            }, 500);
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
                    <label className="block mb-2">Nama Ilmiah</label>
                    <input 
                        type="text"
                        value={formData.scientific_name}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            scientific_name: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                        required
                    />
                </div>

                <div>
                    <label className="block mb-2">Tingkat Takson</label>
                    <input 
                        type="text"
                        value={formData.taxon_rank}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            taxon_rank: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                        required
                    />
                </div>

                <div>
                    <label className="block mb-2">Kingdom</label>
                    <input 
                        type="text"
                        value={formData.kingdom}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            kingdom: e.target.value
                        }))}
                        className="w-full p-2 border rounded"
                        required
                    />
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

    return (
        <div className="p-4 mt-16">
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
                        Isi Form Sekaligus ({selectedCards.length})
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
        </div>
    );
}

export default MediaUpload;