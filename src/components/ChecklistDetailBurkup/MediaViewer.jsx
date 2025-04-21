import React, { useState } from 'react';

const MediaViewer = ({ images, sounds }) => {
    const [selectedImage, setSelectedImage] = useState(null);

    return (
        <div className="space-y-4">
            {/* Image Gallery */}
            <div className="grid grid-cols-2 gap-2">
                {images?.map((image, index) => (
                    <div
                        key={image.id}
                        className="relative aspect-square cursor-pointer"
                        onClick={() => setSelectedImage(image)}
                    >
                        <img
                            src={image.url}
                            alt={`Gambar ${index + 1}`}
                            className="w-full h-full object-cover rounded-lg"
                        />
                    </div>
                ))}
            </div>

            {/* Sound Player */}
            {sounds?.length > 0 && (
                <div className="space-y-2">
                    <h3 className="font-semibold">Rekaman Suara</h3>
                    {sounds.map((sound) => (
                        <div key={sound.id} className="p-2 bg-gray-50 rounded-lg">
                            <audio controls className="w-full">
                                <source src={sound.url} type="audio/mpeg" />
                                Browser Anda tidak mendukung pemutaran audio.
                            </audio>
                            {sound.spectrogram && (
                                <img
                                    src={sound.spectrogram}
                                    alt="Spektrogram"
                                    className="mt-2 w-full"
                                />
                            )}
                        </div>
                    ))}
                </div>
            )}

            {/* Image Modal */}
            {selectedImage && (
                <div
                    className="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center p-4"
                    onClick={() => setSelectedImage(null)}
                >
                    <div className="max-w-4xl w-full">
                        <img
                            src={selectedImage.url}
                            alt="Gambar diperbesar"
                            className="w-full h-auto max-h-[90vh] object-contain"
                        />
                    </div>
                </div>
            )}
        </div>
    );
};

export default MediaViewer;
