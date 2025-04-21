import React, { useState, useEffect, useCallback } from 'react';
import { MapContainer, TileLayer, Rectangle, useMap, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import axios from 'axios';

// Import marker icons sebagai URL
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Fix untuk icon marker Leaflet
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

function ChecklistMap({ checklist }) {
    const [relatedLocations, setRelatedLocations] = useState([]);
    const [visibleGrid, setVisibleGrid] = useState('small');
    const [gridData, setGridData] = useState({
        main: null,
        related: []
    });

    const latitude = parseFloat(checklist?.latitude) || 0;
    const longitude = parseFloat(checklist?.longitude) || 0;

    // Fetch lokasi lain dengan taxa_id yang sama
    useEffect(() => {
        const fetchRelatedLocations = async () => {
            if (checklist?.taxa_id) {
                try {
                    const response = await axios.get(`http://localhost:8000/api/observations/related-locations/${checklist.taxa_id}`);
                    // Filter out current location
                    const filteredLocations = response.data.filter(loc => 
                        loc.id !== checklist.id
                    );
                    setRelatedLocations(filteredLocations);
                } catch (error) {
                    console.error('Error fetching related locations:', error);
                }
            }
        };
        
        fetchRelatedLocations();
    }, [checklist?.taxa_id]);

    // Fungsi untuk membuat bounds yang mencakup semua marker
    const getBounds = () => {
        const points = [
            [latitude, longitude],
            ...relatedLocations.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)])
        ];
        
        return L.latLngBounds(points).pad(0.1); // Tambah padding 10%
    };

    // Komponen ZoomHandler yang terpisah
    const ZoomHandler = () => {
        const map = useMap();
        
        useEffect(() => {
            if (!map) return;

            const handleZoomChange = () => {
                const zoom = map.getZoom();
                if (zoom > 14) {
                    setVisibleGrid('small');
                } else if (zoom > 12) {
                    setVisibleGrid('medium');
                } else if (zoom > 10) {
                    setVisibleGrid('large');
                } else if (zoom > 8) {
                    setVisibleGrid('extraLarge');
                } else {
                    setVisibleGrid('superLarge');
                }
            };

            map.on('zoomend', handleZoomChange);
            return () => {
                map.off('zoomend', handleZoomChange);
            };
        }, [map]);

        return null;
    };

    // Fungsi untuk membuat single grid
    const createSingleGrid = useCallback((lat, lng, gridSize) => {
        if (!lat || !lng || isNaN(lat) || isNaN(lng)) return null;
        
        const halfSize = gridSize / 2;
        return {
            bounds: [
                [lat - halfSize, lng - halfSize],
                [lat + halfSize, lng + halfSize]
            ],
            center: [lat, lng]
        };
    }, []);

    // Inisialisasi grid data
    useEffect(() => {
        if (latitude && longitude) {
            const mainGrids = {
                small: createSingleGrid(latitude, longitude, 0.007),      // ~50m
                medium: createSingleGrid(latitude, longitude, 0.02),      // ~100m
                large: createSingleGrid(latitude, longitude, 0.05),       // ~500m
                extraLarge: createSingleGrid(latitude, longitude, 0.2),   // ~1km
                superLarge: createSingleGrid(latitude, longitude, 0.3)    // ~5km
            };

            const relatedGrids = relatedLocations.map(loc => ({
                small: createSingleGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.007),
                medium: createSingleGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.02),
                large: createSingleGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.05),
                extraLarge: createSingleGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.2),
                superLarge: createSingleGrid(parseFloat(loc.latitude), parseFloat(loc.longitude), 0.4)
            }));

            setGridData({
                main: mainGrids,
                related: relatedGrids
            });
        }
    }, [latitude, longitude, relatedLocations, createSingleGrid]);

    // Fungsi untuk mendapatkan warna grid
    const getGridColor = useCallback((bounds, center) => {
        const isMarkerGrid = center[0] >= bounds[0][0] && 
                           center[0] <= bounds[1][0] && 
                           center[1] >= bounds[0][1] && 
                           center[1] <= bounds[1][1];
        return isMarkerGrid ? 'rgba(227, 26, 28, 0.5)' : 'rgba(254, 180, 76, 0.2)';
    }, []);

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
            maxZoom={14}
        >
            <TileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution='&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            />
            
            <ZoomHandler />
            
            {gridData.main && gridData.main[visibleGrid] && (
                <Rectangle
                    bounds={gridData.main[visibleGrid].bounds}
                    color={getGridColor(gridData.main[visibleGrid].bounds, [latitude, longitude])}
                    weight={1}
                    fillOpacity={0.5}
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

            {gridData.related?.map((locationGrids, locationIndex) => {
                const loc = relatedLocations[locationIndex];
                const grid = locationGrids[visibleGrid];
                if (!grid) return null;

                return (
                    <Rectangle
                        key={`related-grid-${locationIndex}`}
                        bounds={grid.bounds}
                        color={getGridColor(grid.bounds, [parseFloat(loc.latitude), parseFloat(loc.longitude)])}
                        weight={1}
                        fillOpacity={0.5}
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

export default ChecklistMap;