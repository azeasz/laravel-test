import React, { useState, useEffect } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
    faDna, 
    faTree, 
    faNoteSticky, 
    faMusic, 
    faXmark 
} from '@fortawesome/free-solid-svg-icons';

function BulkEditModal({ isOpen, onClose, onSave, selectedItems }) {
    const [tempFormData, setTempFormData] = useState({
        scientific_name: '',
        habitat: '',
        description: '',
        type_sound: '',
        source: 'live',
        status: 'pristine'
    });

    // Cek apakah ada file audio yang dipilih
    const hasAudioFiles = selectedItems.some(item => item.type === 'audio');
    const hasImageFiles = selectedItems.some(item => item.type === 'image');

    // Animasi untuk modal
    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }
        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [isOpen]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setTempFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // Reset form ketika modal ditutup
    useEffect(() => {
        if (!isOpen) {
            setTempFormData({
                scientific_name: '',
                habitat: '',
                description: '',
                type_sound: '',
                source: 'live',
                status: 'pristine'
            });
        }
    }, [isOpen]);

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-800 overflow-y-auto">
            <div 
                className="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity"
                onClick={onClose}
            />
            
            <div className="flex min-h-full items-center justify-center p-4">
                <div className="relative w-full max-w-2xl transform rounded-xl bg-white shadow-2xl transition-all">
                    {/* Header */}
                    <div className="flex items-center justify-between border-b px-6 py-4">
                        <h3 className="text-xl font-semibold text-gray-900">
                            Isi Form Sekaligus ({selectedItems.length} item)
                        </h3>
                        <button 
                            onClick={onClose}
                            className="rounded-full p-1 hover:bg-gray-100"
                        >
                            <FontAwesomeIcon icon={faXmark} className="h-6 w-6 text-gray-500" />
                        </button>
                    </div>

                    {/* Body */}
                    <div className="px-6 py-4 space-y-4">
                        {/* Form Fields untuk Semua Tipe */}
                        <div className="space-y-4">
                            {/* Nama Taksa */}
                            <div className="form-group">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Spesies
                                </label>
                                <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-purple-500 focus-within:border-purple-500 transition-colors">
                                    <FontAwesomeIcon icon={faDna} className="text-gray-400" />
                                    <input
                                        type="text"
                                        name="scientific_name"
                                        className="w-full focus:outline-none"
                                        value={tempFormData.scientific_name}
                                        onChange={handleChange}
                                        placeholder="Masukkan nama taksa"
                                    />
                                </div>
                            </div>

                            {/* Habitat */}
                            <div className="form-group">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Habitat
                                </label>
                                <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-purple-500 focus-within:border-purple-500 transition-colors">
                                    <FontAwesomeIcon icon={faTree} className="text-gray-400" />
                                    <input
                                        type="text"
                                        name="habitat"
                                        className="w-full focus:outline-none"
                                        value={tempFormData.habitat}
                                        onChange={handleChange}
                                        placeholder="Masukkan habitat"
                                    />
                                </div>
                            </div>

                            {/* Keterangan */}
                            <div className="form-group">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <div className="flex space-x-3 rounded-lg border border-gray-200 p-3 hover:border-purple-500 focus-within:border-purple-500 transition-colors">
                                    <FontAwesomeIcon icon={faNoteSticky} className="text-gray-400 mt-1" />
                                    <textarea
                                        name="description"
                                        rows="3"
                                        className="w-full focus:outline-none resize-none"
                                        value={tempFormData.description}
                                        onChange={handleChange}
                                        placeholder="Masukkan keterangan"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Form Fields khusus Audio */}
                        {hasAudioFiles && (
                            <div className="space-y-4 border-t pt-4">
                                <h4 className="font-medium text-gray-900">Pengaturan Audio</h4>
                                
                                {/* Tipe Suara */}
                                <div className="form-group">
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Tipe Suara
                                    </label>
                                    <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-purple-500 focus-within:border-purple-500 transition-colors">
                                        <FontAwesomeIcon icon={faMusic} className="text-gray-400" />
                                        <select
                                            name="type_sound"
                                            className="w-full focus:outline-none bg-transparent"
                                            value={tempFormData.type_sound}
                                            onChange={handleChange}
                                        >
                                            <option value="">Pilih tipe suara</option>
                                            <option value="song">Song</option>
                                            <option value="call">Call</option>
                                        </select>
                                    </div>
                                </div>

                                {/* Radio Groups */}
                            </div>
                        )}
                    </div>

                    {/* Footer */}
                    <div className="border-t px-6 py-4 flex justify-end space-x-3">
                        <button
                            onClick={onClose}
                            className="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            onClick={() => {
                                onSave(tempFormData);
                                onClose();
                            }}
                            className="px-4 py-2 rounded-lg bg-purple-500 text-white hover:bg-purple-600 transition-colors"
                        >
                            Terapkan ke Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default BulkEditModal;