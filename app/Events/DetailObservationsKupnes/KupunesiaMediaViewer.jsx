import React, { useState, useRef, useEffect } from 'react';
import ImageGallery from 'react-image-gallery';
import { Swiper, SwiperSlide } from 'swiper/react';
import { FreeMode } from 'swiper/modules';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faExpand, faCompress } from '@fortawesome/free-solid-svg-icons';
import 'swiper/css';
import 'swiper/css/free-mode';
import 'react-image-gallery/styles/css/image-gallery.css';

function KupunesiaMediaViewer({ checklist }) {
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [currentIndex, setCurrentIndex] = useState(0);
    const galleryRef = useRef(null);
    const containerRef = useRef(null);

    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            containerRef.current?.requestFullscreen();
            setIsFullscreen(true);
        } else {
            document.exitFullscreen();
            setIsFullscreen(false);
        }
    };

    useEffect(() => {
        const handleFullscreenChange = () => {
            setIsFullscreen(!!document.fullscreenElement);
        };

        document.addEventListener('fullscreenchange', handleFullscreenChange);
        return () => {
            document.removeEventListener('fullscreenchange', handleFullscreenChange);
        };
    }, []);

    const items = checklist?.medias?.map(media => ({
        original: media.url,
        thumbnail: media.url,
        description: media.caption || '',
        renderItem: () => (
            <div className="relative">
                <img
                    src={media.url}
                    alt={media.caption || 'Gambar observasi'}
                    className="w-full h-auto max-h-[600px] object-contain"
                />
                <div className="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-sm">
                    CC BY-NC
                </div>
            </div>
        )
    })) || [];

    if (!items.length) {
        return (
            <div className="flex items-center justify-center h-[400px] bg-gray-100 rounded-lg">
                <p className="text-gray-500">Tidak ada media tersedia</p>
            </div>
        );
    }

    return (
        <div ref={containerRef} className="media-viewer-container relative">
            <ImageGallery
                ref={galleryRef}
                items={items}
                showPlayButton={false}
                showFullscreenButton={true}
                showNav={items.length > 1}
                showThumbnails={items.length > 1}
                showBullets={items.length > 1}
                startIndex={currentIndex}
                onSlide={(index) => setCurrentIndex(index)}
                renderFullscreenButton={() => (
                    <button
                        onClick={toggleFullscreen}
                        className="image-gallery-fullscreen-button"
                        aria-label="Toggle fullscreen"
                    >
                        <FontAwesomeIcon
                            icon={isFullscreen ? faCompress : faExpand}
                            className="text-white text-xl"
                        />
                    </button>
                )}
            />
        </div>
    );
}

export default KupunesiaMediaViewer;
