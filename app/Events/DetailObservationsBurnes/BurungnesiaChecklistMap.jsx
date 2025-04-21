import React, { useState, useEffect } from 'react';
import { MapContainer, TileLayer, Rectangle, useMap, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { apiFetch } from '../../../utils/api';

// Fix untuk icon marker Leaflet
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: '/marker-icon-2x.png',
    iconUrl: '/marker-icon.png',
    shadowUrl: '/marker-shadow.png',
});

function BurungnesiaChecklistMap({ checklist }) {
    const [relatedLocations, setRelatedLocations] = useState([]);
    const [visibleGrid, setVisibleGrid] = useState('small');
    const [gridData, setGridData] = useState({
        main: null,
        related: []
    });

    const latitude = parseFloat(checklist?.latitude) || 0;
    const longitude = parseFloat(checklist?.longitude) || 0;

    useEffect(() => {
        if (checklist?.taxa_id) {
            fetchRelatedLocations();
        }
    }, [checklist?.taxa_id]);

    const fetchRelatedLocations = async () => {
        try {
            const response = await apiFetch(`/burungnesia/observations/related-locations/${checklist.taxa_id}`);
            const data = await response.json();
            if (data.success) {
                const filteredLocations = data.data.filter(loc => loc.id !== checklist.id);
                setRelatedLocations(filteredLocations);
            }
        } catch (error) {
            console.error('Error fetching related locations:', error);
            setRelatedLocations([]);
        }
    };

    const createGrid = (lat, lng, size) => {
        const halfSize = size / 2;
        return {
            bounds: [
                [lat - halfSize, lng - halfSize],
                [lat + halfSize, lng + halfSize]
            ],
            center: [lat, lng]
        };
    };

    useEffect(() => {
        if (latitude && longitude) {
            const mainGrids = {
                small: createGrid(latitude, longitude, 0.007),
                medium: createGrid(latitude, longitude, 0.02),
                large: createGrid(latitude, longitude, 0.05),
                extraLarge: createGrid(latitude, longitude, 0.2),
                superLarge: createGrid(latitude, longitude, 0.3)
            };

            const relatedGrids = relatedLocations.map(loc => ({
                small: createGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.007),
                medium: createGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.02),
                large: createGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.05),
                extraLarge: createGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.2),
                superLarge: createGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.3)
            }));

            setGridData({
                main: mainGrids,
                related: relatedGrids
            });
        }
    }, [latitude, longitude, relatedLocations]);

    const ZoomHandler = () => {
        const map = useMap();

        useEffect(() => {
            const handleZoomChange = () => {
                const zoom = map.getZoom();
                if (zoom > 14) setVisibleGrid('small');
                else if (zoom > 12) setVisibleGrid('medium');
                else if (zoom > 10) setVisibleGrid('large');
                else if (zoom > 8) setVisibleGrid('extraLarge');
                else setVisibleGrid('superLarge');
            };

            map.on('zoomend', handleZoomChange);
            return () => map.off('zoomend', handleZoomChange);
        }, [map]);

        return null;
    };

    const getBounds = () => {
        const points = [
            [latitude, longitude],
            ...relatedLocations.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)])
        ];
        return L.latLngBounds(points).pad(0.1);
    };

    if (!latitude || !longitude || isNaN(latitude) || isNaN(longitude)) {
        return (
            <div className="flex items-center justify-center h-[400px] bg-gray-100 rounded-lg">
                <p className="text-gray-500">Lokasi tidak tersedia</p>
            </div>
        );
    }

    return (
        <MapContainer
            bounds={getBounds()}
            style={{ height: "400px", width: "100%" }}
            className="rounded-lg shadow-md"
            maxZoom={18}
        >
            <TileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution='&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            />

            <ZoomHandler />

            {gridData.main && gridData.main[visibleGrid] && (
                <Rectangle
                    bounds={gridData.main[visibleGrid].bounds}
                    pathOptions={{
                        color: 'rgba(227, 26, 28, 0.5)',
                        weight: 1,
                        fillOpacity: 0.5
                    }}
                >
                    <Popup>
                        <div className="p-2">
                            <h3 className="font-semibold">{checklist.scientific_name || 'Spesies tidak diketahui'}</h3>
                            <p className="text-sm text-gray-600">
                                Lat: {latitude.toFixed(6)}<br />
                                Long: {longitude.toFixed(6)}
                            </p>
                        </div>
                    </Popup>
                </Rectangle>
            )}

            {gridData.related?.map((locationGrids, index) => {
                const loc = relatedLocations[index];
                const grid = locationGrids[visibleGrid];
                if (!grid) return null;

                return (
                    <Rectangle
                        key={`related-grid-${index}`}
                        bounds={grid.bounds}
                        pathOptions={{
                            color: 'rgba(254, 180, 76, 0.2)',
                            weight: 1,
                            fillOpacity: 0.5
                        }}
                    >
                        <Popup>
                            <div className="p-2">
                                <h3 className="font-semibold">{loc.scientific_name || 'Spesies tidak diketahui'}</h3>
                                <p className="text-sm text-gray-600">
                                    Lat: {parseFloat(loc.latitude).toFixed(6)}<br />
                                    Long: {parseFloat(loc.longitude).toFixed(6)}
                                </p>
                                <p className="text-xs text-gray-500 mt-1">
                                    Tanggal: {new Date(loc.created_at).toLocaleDateString('id-ID')}
                                </p>
                            </div>
                        </Popup>
                    </Rectangle>
                );
            })}
        </MapContainer>
    );
}

export default BurungnesiaChecklistMap;
