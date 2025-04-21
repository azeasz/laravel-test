import React, { useState, useRef, useEffect } from 'react';
import LocationPicker from './Observations/LocationPicker';
import Modal from './Observations/LPModal';
import { Swiper, SwiperSlide } from 'swiper/react';
import { FreeMode } from 'swiper/modules';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
    faDna,
    faCalendar,
    faLocationDot,
    faTree,
    faNoteSticky,
    faMusic,
    faPlay,
    faPause,
    faExpand,
    faSpinner
} from '@fortawesome/free-solid-svg-icons';
import 'swiper/css';
import 'swiper/css/free-mode';
import './MediaCard.css';
import SpectrogramModal from './SpectrogramModal';
import QualityBadge from './QualityBadge';

function MediaCard({ observation, isSelected, onSelect, onUpdate, onDelete, bulkFormData, qualityGrade }) {
    // State declarations
    const [isLocationModalOpen, setIsLocationModalOpen] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [audioTime, setAudioTime] = useState(0);
    const [audioDuration, setAudioDuration] = useState(0);
    const [isPlaying, setIsPlaying] = useState(false);
    const [isBuffering, setIsBuffering] = useState(false);
    const [error, setError] = useState(null);
    const [isSpectrogramModalOpen, setIsSpectrogramModalOpen] = useState(false);
    const [audioUrl, setAudioUrl] = useState('');
    const [spectrogramWidth, setSpectrogramWidth] = useState(0);

    // Refs
    const audioRef = useRef(null);
    const spectrogramRef = useRef(null);
    const progressRef = useRef(null);
    const audioUrlRef = useRef(null);
    const spectrogramContainerRef = useRef(null);
    const swiperRef = useRef(null);

    // Form data state
    const [formData, setFormData] = useState({
        scientific_name: '',
        date: '',
        location: '',
        habitat: '',
        description: '',
        type_sound: '',
        source: '',
        status: ''
    });

    // Effect untuk bulk form data
    useEffect(() => {
        if (isSelected && bulkFormData) {
            setFormData(prev => ({
                ...prev,
                ...bulkFormData
            }));
        }
    }, [bulkFormData, isSelected]);

    // Audio URL handling
    const getAudioUrl = () => {
        if (!observation?.file) return '';
        return URL.createObjectURL(observation.file);
    };

    useEffect(() => {
        const url = getAudioUrl();
        setAudioUrl(url);

        return () => {
            if (url) URL.revokeObjectURL(url);
        };
    }, [observation.file]);

    // Audio event listeners
    useEffect(() => {
        if (!audioRef.current) return;

        const audio = audioRef.current;

        const handlePlay = () => {
            setIsPlaying(true);
            setError(null);
        };

        const handlePause = () => setIsPlaying(false);

        const handleEnded = () => {
            setIsPlaying(false);
            if (progressRef.current) {
                progressRef.current.style.width = '0%';
            }
            if (swiperRef.current?.swiper) {
                swiperRef.current.swiper.setTranslate(0);
            }
        };

        const handleTimeUpdate = () => {
            setAudioTime(audio.currentTime);
            const progress = (audio.currentTime / audio.duration) * 100;
            if (progressRef.current) {
                progressRef.current.style.width = `${progress}%`;
            }
            if (swiperRef.current?.swiper) {
                const translateX = (audio.currentTime / audio.duration) * spectrogramWidth;
                swiperRef.current.swiper.setTranslate(-translateX);
            }
        };

        const handleLoadedMetadata = () => {
            setAudioDuration(audio.duration);
        };

        const handleWaiting = () => setIsBuffering(true);
        const handleCanPlay = () => setIsBuffering(false);

        const handleError = (e) => {
            console.error('Audio error:', e);
            setError('Terjadi kesalahan saat memutar audio');
            setIsPlaying(false);
            setIsBuffering(false);
        };

        // Add event listeners
        audio.addEventListener('play', handlePlay);
        audio.addEventListener('pause', handlePause);
        audio.addEventListener('ended', handleEnded);
        audio.addEventListener('timeupdate', handleTimeUpdate);
        audio.addEventListener('loadedmetadata', handleLoadedMetadata);
        audio.addEventListener('waiting', handleWaiting);
        audio.addEventListener('canplay', handleCanPlay);
        audio.addEventListener('error', handleError);

        // Cleanup
        return () => {
            audio.removeEventListener('play', handlePlay);
            audio.removeEventListener('pause', handlePause);
            audio.removeEventListener('ended', handleEnded);
            audio.removeEventListener('timeupdate', handleTimeUpdate);
            audio.removeEventListener('loadedmetadata', handleLoadedMetadata);
            audio.removeEventListener('waiting', handleWaiting);
            audio.removeEventListener('canplay', handleCanPlay);
            audio.removeEventListener('error', handleError);
        };
    }, [spectrogramWidth]);

    // Spectrogram width handling
    useEffect(() => {
        if (spectrogramRef.current) {
            const updateWidth = () => {
                setSpectrogramWidth(spectrogramRef.current.scrollWidth);
            };
            updateWidth();
            window.addEventListener('resize', updateWidth);
            return () => window.removeEventListener('resize', updateWidth);
        }
    }, [observation.spectrogramUrl]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        const updatedData = {
            ...formData,
            [name]: value
        };
        setFormData(updatedData);

        // Kirim update ke parent component
        const dataToUpdate = {
            ...updatedData,
            latitude: observation.latitude,
            longitude: observation.longitude,
            locationName: observation.locationName
        };
        onUpdate(dataToUpdate);
    };

    const handleLocationSave = (lat, lng, locationName) => {
        const locationData = {
            ...formData,
            latitude: lat,
            longitude: lng,
            locationName: locationName
        };
        setFormData(locationData);
        onUpdate(locationData);
        setIsLocationModalOpen(false);
    };

    const togglePlay = () => {
        if (!audioRef.current) return;

        if (isPlaying) {
            audioRef.current.pause();
        } else {
            audioRef.current.play();
        }
    };

    const handleSpectrogramClick = (e) => {
        if (!audioRef.current || !spectrogramRef.current) return;

        const rect = spectrogramRef.current.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const width = rect.width;
        const clickPosition = x / width;

        audioRef.current.currentTime = audioRef.current.duration * clickPosition;
    };

    // Helper function untuk format waktu
    const formatTime = (seconds) => {
        if (!seconds) return '0:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    return (
        <div className={`media-card ${isSelected ? 'selected' : ''}`}>
            {/* Card Header */}
            <div className="card-header">
                <input
                    type="checkbox"
                    checked={isSelected}
                    onChange={() => onSelect()}
                    className="checkbox"
                />
                {qualityGrade && <QualityBadge grade={qualityGrade} />}
            </div>

            {/* Media Content */}
            <div className="media-content">
                {observation.type === 'image' ? (
                    <img
                        src={URL.createObjectURL(observation.file)}
                        alt="Observation"
                        className="media-preview"
                    />
                ) : (
                    <div className="audio-container">
                        <audio ref={audioRef} src={audioUrl} />
                        <div className="audio-controls">
                            <button
                                onClick={togglePlay}
                                className="play-button"
                                disabled={isBuffering}
                            >
                                {isBuffering ? (
                                    <FontAwesomeIcon icon={faSpinner} spin />
                                ) : (
                                    <FontAwesomeIcon icon={isPlaying ? faPause : faPlay} />
                                )}
                            </button>
                            <div className="time-display">
                                {formatTime(audioTime)} / {formatTime(audioDuration)}
                            </div>
                        </div>
                        {observation.spectrogramUrl && (
                            <div
                                className="spectrogram-container"
                                ref={spectrogramContainerRef}
                            >
                                <Swiper
                                    ref={swiperRef}
                                    slidesPerView="auto"
                                    freeMode={true}
                                    modules={[FreeMode]}
                                    className="spectrogram-swiper"
                                >
                                    <SwiperSlide>
                                        <img
                                            ref={spectrogramRef}
                                            src={observation.spectrogramUrl}
                                            alt="Spectrogram"
                                            className="spectrogram"
                                            onClick={handleSpectrogramClick}
                                        />
                                        <div
                                            className="expand-button"
                                            onClick={() => setIsSpectrogramModalOpen(true)}
                                        >
                                            <FontAwesomeIcon icon={faExpand} />
                                        </div>
                                    </SwiperSlide>
                                </Swiper>
                                <div className="progress-bar">
                                    <div ref={progressRef} className="progress" />
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>

            {/* Form Fields */}
            <div className="form-fields">
                {/* Scientific Name */}
                <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-gray-300">
                    <FontAwesomeIcon icon={faDna} className="text-gray-500 w-5 h-5" />
                    <input
                        type="text"
                        name="scientific_name"
                        placeholder="Nama ilmiah"
                        className="w-full focus:outline-none text-gray-700"
                        value={formData.scientific_name}
                        onChange={handleInputChange}
                    />
                </div>

                {/* Date */}
                <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-gray-300">
                    <FontAwesomeIcon icon={faCalendar} className="text-gray-500 w-5 h-5" />
                    <input
                        type="date"
                        name="date"
                        className="w-full focus:outline-none text-gray-700"
                        value={formData.date}
                        onChange={handleInputChange}
                    />
                </div>

                {/* Location */}
                <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-gray-300">
                    <FontAwesomeIcon icon={faLocationDot} className="text-gray-500 w-5 h-5" />
                    <button
                        onClick={() => setIsLocationModalOpen(true)}
                        className="w-full text-left text-gray-700 hover:text-gray-900"
                    >
                        {observation.locationName || 'Pilih lokasi'}
                    </button>
                </div>

                {/* Habitat */}
                <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-gray-300">
                    <FontAwesomeIcon icon={faTree} className="text-gray-500 w-5 h-5" />
                    <input
                        type="text"
                        name="habitat"
                        placeholder="Habitat"
                        className="w-full focus:outline-none text-gray-700"
                        value={formData.habitat}
                        onChange={handleInputChange}
                    />
                </div>

                {/* Description */}
                <div className="flex space-x-3 rounded-lg border border-gray-200 p-3 hover:border-gray-300">
                    <FontAwesomeIcon icon={faNoteSticky} className="text-gray-500 w-5 h-5 mt-1" />
                    <textarea
                        name="description"
                        placeholder="Keterangan"
                        rows="3"
                        className="w-full focus:outline-none text-gray-700 resize-none"
                        value={formData.description}
                        onChange={handleInputChange}
                    />
                </div>

                {/* Audio-specific fields */}
                {observation.type === 'audio' && (
                    <>
                        <div className="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:border-gray-300">
                            <FontAwesomeIcon icon={faMusic} className="text-gray-500 w-5 h-5" />
                            <select
                                name="type_sound"
                                className="w-full focus:outline-none text-gray-700 bg-transparent"
                                value={formData.type_sound}
                                onChange={handleInputChange}
                            >
                                <option value="">Pilih tipe suara</option>
                                <option value="song">Song</option>
                                <option value="call">Call</option>
                            </select>
                        </div>
                    </>
                )}
            </div>

            {/* Action Buttons */}
            <div className="action-buttons">
                <button
                    onClick={onDelete}
                    className="delete-button"
                >
                    Hapus
                </button>
            </div>

            {/* Modals */}
            <Modal
                isOpen={isLocationModalOpen}
                onClose={() => setIsLocationModalOpen(false)}
            >
                <LocationPicker onSave={handleLocationSave} />
            </Modal>

            <SpectrogramModal
                isOpen={isSpectrogramModalOpen}
                onClose={() => setIsSpectrogramModalOpen(false)}
                spectrogramUrl={observation.spectrogramUrl}
            />
        </div>
    );
}

export default MediaCard;
