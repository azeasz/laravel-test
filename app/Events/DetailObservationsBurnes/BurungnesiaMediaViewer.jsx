import React, { useState, useRef, useEffect } from 'react';
import ImageGallery from 'react-image-gallery';
import { Swiper, SwiperSlide } from 'swiper/react';
import { FreeMode } from 'swiper/modules';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlay, faPause, faExpand, faCompress, faVolumeUp } from '@fortawesome/free-solid-svg-icons';
import 'swiper/css';
import 'swiper/css/free-mode';
import 'react-image-gallery/styles/css/image-gallery.css';

function BurungnesiaMediaViewer({ checklist }) {
    const [isPlaying, setIsPlaying] = useState(false);
    const [currentTime, setCurrentTime] = useState(0);
    const [duration, setDuration] = useState(0);
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [volume, setVolume] = useState(1);
    const [isMuted, setIsMuted] = useState(false);

    const audioRef = useRef(null);
    const progressRef = useRef(null);
    const spectrogramRef = useRef(null);
    const galleryRef = useRef(null);

    const formatTime = (seconds) => {
        if (!seconds) return '0:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    useEffect(() => {
        return () => {
            if (audioRef.current) {
                audioRef.current.pause();
                audioRef.current.currentTime = 0;
            }
        };
    }, []);

    const handlePlayPause = () => {
        if (audioRef.current) {
            if (isPlaying) {
                audioRef.current.pause();
            } else {
                audioRef.current.play();
            }
            setIsPlaying(!isPlaying);
        }
    };

    const handleTimeUpdate = () => {
        if (audioRef.current) {
            setCurrentTime(audioRef.current.currentTime);
        }
    };

    const handleLoadedMetadata = () => {
        if (audioRef.current) {
            setDuration(audioRef.current.duration);
        }
    };

    const handleProgressClick = (e) => {
        if (audioRef.current && progressRef.current) {
            const rect = progressRef.current.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const width = rect.width;
            const percentage = x / width;
            const newTime = percentage * duration;
            audioRef.current.currentTime = newTime;
            setCurrentTime(newTime);
        }
    };

    const handleVolumeChange = (e) => {
        const newVolume = parseFloat(e.target.value);
        setVolume(newVolume);
        if (audioRef.current) {
            audioRef.current.volume = newVolume;
            setIsMuted(newVolume === 0);
        }
    };

    const toggleMute = () => {
        if (audioRef.current) {
            if (isMuted) {
                audioRef.current.volume = volume;
                setIsMuted(false);
            } else {
                audioRef.current.volume = 0;
                setIsMuted(true);
            }
        }
    };

    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            spectrogramRef.current?.requestFullscreen();
            setIsFullscreen(true);
        } else {
            document.exitFullscreen();
            setIsFullscreen(false);
        }
    };

    const items = checklist?.medias?.map(media => {
        if (media.type === 'audio') {
            return {
                original: media.url,
                thumbnail: media.spectrogram_url,
                renderItem: () => (
                    <div className="audio-player-container">
                        <audio
                            ref={audioRef}
                            src={media.url}
                            onTimeUpdate={handleTimeUpdate}
                            onLoadedMetadata={handleLoadedMetadata}
                            onEnded={() => setIsPlaying(false)}
                        />

                        <div className="spectrogram-container" ref={spectrogramRef}>
                            <div className="audio-controls">
                                <button onClick={handlePlayPause}>
                                    <FontAwesomeIcon icon={isPlaying ? faPause : faPlay} />
                                </button>

                                <div className="time-display">
                                    {formatTime(currentTime)} / {formatTime(duration)}
                                </div>

                                <div
                                    className="progress-bar"
                                    ref={progressRef}
                                    onClick={handleProgressClick}
                                >
                                    <div
                                        className="progress-fill"
                                        style={{ width: `${(currentTime / duration) * 100}%` }}
                                    />
                                </div>

                                <div className="volume-control">
                                    <button onClick={toggleMute}>
                                        <FontAwesomeIcon icon={faVolumeUp} />
                                    </button>
                                    <input
                                        type="range"
                                        min="0"
                                        max="1"
                                        step="0.1"
                                        value={isMuted ? 0 : volume}
                                        onChange={handleVolumeChange}
                                    />
                                </div>

                                <button onClick={toggleFullscreen}>
                                    <FontAwesomeIcon icon={isFullscreen ? faCompress : faExpand} />
                                </button>
                            </div>

                            <img
                                src={media.spectrogram_url}
                                alt="Audio spectrogram"
                                className="spectrogram-image"
                            />
                        </div>
                    </div>
                )
            };
        } else {
            return {
                original: media.url,
                thumbnail: media.url,
                description: media.caption || ''
            };
        }
    }) || [];

    return (
        <div className="media-viewer-container">
            <ImageGallery
                ref={galleryRef}
                items={items}
                showPlayButton={false}
                showFullscreenButton={true}
                showNav={items.length > 1}
                showThumbnails={items.length > 1}
                showBullets={items.length > 1}
                startIndex={currentIndex}
                onSlide={(index) => {
                    setCurrentIndex(index);
                    if (isPlaying) {
                        setIsPlaying(false);
                        audioRef.current?.pause();
                    }
                }}
            />
        </div>
    );
}

export default BurungnesiaMediaViewer;
