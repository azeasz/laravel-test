import React from 'react';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix untuk icon marker Leaflet
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: '/marker-icon-2x.png',
    iconUrl: '/marker-icon.png',
    shadowUrl: '/marker-shadow.png',
});

const ChecklistMap = ({ latitude, longitude, locationName }) => {
    if (!latitude || !longitude) return null;

    const position = [latitude, longitude];

    return (
        <div className="h-[400px] w-full rounded-lg overflow-hidden">
            <MapContainer
                center={position}
                zoom={13}
                style={{ height: '100%', width: '100%' }}
            >
                <TileLayer
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />
                <Marker position={position}>
                    <Popup>
                        {locationName || 'Lokasi Pengamatan'}
                    </Popup>
                </Marker>
            </MapContainer>
        </div>
    );
};

export default ChecklistMap;
