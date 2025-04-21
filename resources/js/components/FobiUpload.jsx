import React, { useState } from 'react';

function FobiUpload() {
    const [faunaName, setFaunaName] = useState('');
    const [faunaId, setFaunaId] = useState('');
    const [formData, setFormData] = useState({
        latitude: '',
        longitude: '',
        tujuan_pengamatan: '',
        observer: '',
        additional_note: '',
        active: '',
        tgl_pengamatan: '',
        start_time: '',
        end_time: '',
        completed: '',
        count: [],
        notes: [],
        breeding: false,
        breeding_note: [],
        breeding_type_id: [],
        images: [],
        sounds: []
    });
    const [error, setError] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isFileModalOpen, setIsFileModalOpen] = useState(false);
    const [birdList, setBirdList] = useState([]);
    const [editIndex, setEditIndex] = useState(null);

    const handleFaunaNameChange = async (name) => {
        setFaunaName(name);

        if (name.length > 2) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/faunas?name=${encodeURIComponent(name)}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch fauna ID');
                }
                const data = await response.json();
                if (data.fauna_id) {
                    setFaunaId(data.fauna_id);
                }
            } catch (error) {
                console.error('Error fetching fauna ID:', error);
                setError('Error fetching fauna ID');
            }
        }
    };

    const handleInputChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData(prevFormData => ({
            ...prevFormData,
            [name]: type === 'checkbox' ? checked : value
        }));
    };

    const handleFileChange = (e) => {
        const { files } = e.target;
        let newImages = [];
        let newSounds = [];

        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                newImages.push(file);
            } else if (file.type.startsWith('audio/')) {
                newSounds.push(file);
            }
        });

        setFormData(prevFormData => ({
            ...prevFormData,
            images: [...prevFormData.images, ...newImages],
            sounds: [...prevFormData.sounds, ...newSounds]
        }));
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
        const newBird = { ...formData, faunaName, spectrogramUrl: null };
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

        // Fetch ulang faunaId berdasarkan faunaName
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
        const fileData = new FormData();
        formData.images.forEach((image, index) => {
            fileData.append('images[]', image);
        });
        formData.sounds.forEach((sound, index) => {
            fileData.append('sounds[]', sound);
        });

        try {
            const response = await fetch('http://127.0.0.1:8000/api/generate-spectrogram', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
                },
                body: fileData
            });

            if (!response.ok) {
                throw new Error('Terjadi kesalahan saat membuat spektrogram.');
            }

            const data = await response.json();
            const updatedBirdList = birdList.map((bird, index) =>
                index === editIndex ? {
                    ...bird,
                    images: formData.images,
                    sounds: formData.sounds,
                    spectrogramUrl: data.spectrogramUrl || bird.spectrogramUrl
                } : bird
            );
            setBirdList(updatedBirdList);
        } catch (error) {
            console.error('Error generating spectrogram:', error);
            setError(error.message);
        }

        handleFileModalClose();
    };
    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        try {
            const formDataToSend = new FormData();
            formDataToSend.append('fauna_id', faunaId);

            Object.keys(formData).forEach(key => {
                let value = formData[key];
                if (typeof value === 'boolean') {
                    value = value ? '1' : '0';
                }
                if (value !== null && value !== '') {
                    formDataToSend.append(key, value);
                }
            });

            formData.images.forEach((image, index) => {
                formDataToSend.append('images[]', image);
            });
            formData.sounds.forEach((sound, index) => {
                formDataToSend.append('sounds[]', sound);
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
                throw new Error(errorData.error || 'Terjadi kesalahan saat mengunggah data.');
            }

            const data = await response.json();
            if (data.success) {
                alert('Data berhasil diunggah ke kedua database!');
                if (data.spectrogramUrl) {
                    const updatedBirdList = birdList.map((bird, index) =>
                        index === editIndex ? { ...bird, spectrogramUrl: data.spectrogramUrl } : bird
                    );
                    setBirdList(updatedBirdList);
                }
            } else {
                throw new Error('Terjadi kesalahan saat mengunggah data.');
            }
        } catch (error) {
            console.error('Error uploading data:', error);
            setError(error.message);
        }
    };
    return (
        <div className="p-4 md:mt-5">
            <h1 className="text-2xl font-bold mb-4">Upload Fobi Data</h1>
            <form onSubmit={handleSubmit} className="space-y-4">
                <h2 className="text-xl font-semibold">Checklist</h2>
                <input type="text" name="latitude" placeholder="Latitude" required className="border p-2 w-full" onChange={handleInputChange} value={formData.latitude} />
                <input type="text" name="longitude" placeholder="Longitude" required className="border p-2 w-full" onChange={handleInputChange} value={formData.longitude} />
                <input type="number" name="tujuan_pengamatan" placeholder="Tujuan Pengamatan" required className="border p-2 w-full" onChange={handleInputChange} value={formData.tujuan_pengamatan} />
                <input type="text" name="observer" placeholder="Observer" required className="border p-2 w-full" onChange={handleInputChange} value={formData.observer} />
                <input type="text" name="additional_note" placeholder="Additional Note" className="border p-2 w-full" onChange={handleInputChange} value={formData.additional_note} />
                <input type="number" name="active" placeholder="Active" className="border p-2 w-full" onChange={handleInputChange} value={formData.active} />
                <input type="date" name="tgl_pengamatan" placeholder="Tanggal Pengamatan" className="border p-2 w-full" onChange={handleInputChange} value={formData.tgl_pengamatan} />
                <input type="time" name="start_time" placeholder="Start Time" className="border p-2 w-full" onChange={handleInputChange} value={formData.start_time} />
                <input type="time" name="end_time" placeholder="End Time" className="border p-2 w-full" onChange={handleInputChange} value={formData.end_time} />
                <input type="number" name="completed" placeholder="Completed" className="border p-2 w-full" onChange={handleInputChange} value={formData.completed} />

                <button type="button" onClick={handleOpenModal} className="bg-green-500 text-white p-2 rounded">Tambah Jenis</button>
                <button type="submit" className="bg-blue-500 text-white p-2 rounded">Upload Data</button>
            </form>
            {error && <p className="text-red-500 mt-4">{error}</p>}

            {birdList.length > 0 && (
                <div className="mt-8">
                    <h2 className="text-xl font-semibold mb-4">Daftar Burung</h2>
                    <table className="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th className="py-2">Media</th>
                                <th className="py-2">Nama</th>
                                <th className="py-2">Jumlah</th>
                                <th className="py-2">Berbiak</th>
                                <th className="py-2">Catatan</th>
                                <th className="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {birdList.map((bird, index) => (
                                <tr key={index} className="text-center">
                                    <td className="py-2">
                                        {bird.images && bird.images.map((image, i) => (
                                            <img key={i} src={URL.createObjectURL(image)} alt="Foto Burung" className="w-16 h-16" />
                                        ))}
                                        {bird.spectrogramUrl && (
                                            <img src={bird.spectrogramUrl} alt="Spektrogram" className="w-32 h-32" />
                                        )}
                                        {bird.sounds && bird.sounds.map((sound, i) => (
                                            <audio key={i} src={URL.createObjectURL(sound)} controls />
                                        ))}
                                    </td>
                                    <td className="py-2">{bird.faunaName}</td>
                                    <td className="py-2">{bird.count}</td>
                                    <td className="py-2">{bird.breeding ? 'Ya' : 'Tidak'}</td>
                                    <td className="py-2">{bird.notes}</td>
                                    <td className="py-2">
                                        <button onClick={() => handleEditBird(index)} className="bg-blue-500 text-white p-1 rounded">Edit</button>
                                        <button onClick={() => handleDeleteBird(index)} className="bg-red-500 text-white p-1 rounded ml-2">Hapus</button>
                                        <button onClick={() => handleFileModalOpen(index)} className="bg-green-500 text-white p-1 rounded ml-2">Tambah Foto/Audio</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            {isModalOpen && (
                <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                    <div className="bg-white p-6 rounded shadow-lg w-96">
                        <h2 className="text-xl font-semibold mb-4">Tambah Jenis Burung</h2>
                        <input type="text" id="fauna_name" placeholder="Jenis burung" required className="border p-2 w-full mb-2" value={faunaName} onChange={(e) => handleFaunaNameChange(e.target.value)} />
                        <input type="hidden" name="fauna_id" id="fauna_id" value={faunaId} />
                        <input type="text" name="count" placeholder="Jumlah individu" required className="border p-2 w-full mb-2" value={formData.count} onChange={handleInputChange} />
                        <input type="text" name="notes" placeholder="Catatan" className="border p-2 w-full mb-2" value={formData.notes} onChange={handleInputChange} />
                        <div className="flex items-center mb-4">
                            <input type="checkbox" name="breeding" className="mr-2" checked={formData.breeding} onChange={handleInputChange} />
                            <label htmlFor="breeding">Apakah berbiak?</label>
                        </div>
                        <div className="flex justify-between">
                            <button onClick={handleAddBird} className="bg-green-500 text-white p-2 rounded">Simpan</button>
                            <button onClick={() => setIsModalOpen(false)} className="bg-red-500 text-white p-2 rounded">Batal</button>
                        </div>
                    </div>
                </div>
            )}

            {isFileModalOpen && (
                <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                    <div className="bg-white p-6 rounded shadow-lg w-96">
                        <h2 className="text-xl font-semibold mb-4">Tambah Foto/Audio</h2>
                        <input type="file" name="media" onChange={handleFileChange} className="border p-2 w-full mb-2" multiple />
                        <div className="flex justify-between">
                            <button onClick={handleFileSave} className="bg-green-500 text-white p-2 rounded">Simpan</button>
                            <button onClick={handleFileModalClose} className="bg-red-500 text-white p-2 rounded">Batal</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default FobiUpload;
