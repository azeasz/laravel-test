import React, { useEffect, useState, useRef, useCallback } from 'react';
import { MapContainer, TileLayer, useMap } from 'react-leaflet';
import { useMarkers } from '../../hooks/useMarkers';
import { useMapZoom } from '../../hooks/useMapZoom';
import { useSidebar } from '../../hooks/useSidebar';
import { useGridData } from '../../hooks/useGridData';
import { MapControls } from '../Map/MapControls';
import { MapOverlay } from '../Map/GridMarkers';
import { defaultMapConfig, redCircleIcon } from '../../utils/mapHelpers';
import 'leaflet/dist/leaflet.css';
import './MapView.css';
import { ZoomHandler } from '../Map/ZoomHandler';
import { calculateZoomLevel } from '../../utils/geoHelpers';
import { throttle } from 'lodash';
import { Sidebar } from '../Map/Sidebar';


// Optimasi MapController dengan throttling
const MapController = ({ setVisibleBounds, setZoomLevel, setVisibleGrid }) => {
  const map = useMap();
  const updateRef = useRef(null);
  const initialLoadRef = useRef(false);

  useEffect(() => {
    if (!map) return;

    const updateMapState = throttle(() => {
      const currentZoom = map.getZoom();
      const bounds = map.getBounds();
      
      if (bounds && bounds._southWest && bounds._northEast) {
        setVisibleBounds(bounds);
      }
      
      setZoomLevel(currentZoom);

      const gridType = currentZoom > 12 ? 'markers' :
                      currentZoom > 10 ? 'small' :
                      currentZoom >= 8 ? 'medium' :
                      currentZoom >= 6 ? 'large' : 'extraLarge';

      setVisibleGrid(gridType);
    }, 100); // Kurangi throttle untuk initial load

    // Trigger initial update
    if (!initialLoadRef.current) {
      updateMapState();
      initialLoadRef.current = true;
    }

    map.on('moveend', updateMapState);
    map.on('zoomend', updateMapState);
    map.on('load', updateMapState);

    return () => {
      map.off('moveend', updateMapState);
      map.off('zoomend', updateMapState);
      map.off('load', updateMapState);
      updateMapState.cancel();
    };
  }, [map, setVisibleBounds, setZoomLevel, setVisibleGrid]);

  return null;
};

const MapView = ({ searchParams, filterParams, setStats, setLoading: setParentLoading, onReset }) => {
  const mapRef = useRef(null);
  const isMobile = window.innerWidth <= 768;

  // Custom Hooks
  const { mapMarkers, loading, filterMarkers, refreshMarkers, lastUpdate } = useMarkers();
  const { gridData, updateGridData } = useGridData();
  const { currentZoom, handleZoom } = useMapZoom();
  const { sidebarData, toggleSidebar, loadMore } = useSidebar();

  const [visibleBounds, setVisibleBounds] = useState(null);
  const [zoomLevel, setZoomLevel] = useState(5);
  const [visibleGrid, setVisibleGrid] = useState('large');
  const [localLoading, setLocalLoading] = useState(false);

  // Gabungkan loading state lokal dan parent
  const isLoading = localLoading || loading;

  // Effect untuk update loading state parent
  useEffect(() => {
    setParentLoading(isLoading);
  }, [isLoading, setParentLoading]);

  // Fungsi untuk membagi data ke dalam tiles
  const createTileIndex = useCallback((markers, bounds) => {
    if (!markers || !bounds) return new Map();
    
    const tileSize = 1; // 1 derajat per tile
    const tileIndex = new Map();

    markers.forEach(marker => {
      const lat = parseFloat(marker.latitude);
      const lng = parseFloat(marker.longitude);
      
      // Hitung tile key berdasarkan posisi
      const tileX = Math.floor(lng / tileSize);
      const tileY = Math.floor(lat / tileSize);
      const tileKey = `${tileX}:${tileY}`;

      if (!tileIndex.has(tileKey)) {
        tileIndex.set(tileKey, []);
      }
      tileIndex.get(tileKey).push(marker);
    });

    return tileIndex;
  }, []);

  // Effect untuk update data dengan tile-based approach
  useEffect(() => {
    const updateMap = async () => {
      if (!mapMarkers || !visibleBounds) return;

      setLocalLoading(true);
      try {
        const filteredMarkers = filterMarkers(mapMarkers, filterParams, searchParams);
        const tileIndex = createTileIndex(filteredMarkers, visibleBounds);
        const visibleTiles = getVisibleTiles(visibleBounds, zoomLevel);
        const visibleMarkers = [];
        
        visibleTiles.forEach(tileKey => {
          const tileMarkers = tileIndex.get(tileKey) || [];
          visibleMarkers.push(...tileMarkers);
        });

        updateGridData(visibleMarkers, zoomLevel, visibleBounds);
        setLocalLoading(false);
      } catch (error) {
        console.error('Error updating map:', error);
        setLocalLoading(false);
      }
    };

    updateMap();
  }, [mapMarkers, visibleBounds, zoomLevel, filterParams, searchParams]);

  // Helper function untuk mendapatkan visible tiles
  const getVisibleTiles = (bounds, zoom) => {
    if (!bounds) return [];

    const tileSize = 1; // 1 derajat per tile
    const tiles = new Set();

    const minLng = Math.floor(bounds.getWest() / tileSize);
    const maxLng = Math.ceil(bounds.getEast() / tileSize);
    const minLat = Math.floor(bounds.getSouth() / tileSize);
    const maxLat = Math.ceil(bounds.getNorth() / tileSize);

    for (let x = minLng; x <= maxLng; x++) {
      for (let y = minLat; y <= maxLat; y++) {
        tiles.add(`${x}:${y}`);
      }
    }

    return Array.from(tiles);
  };

  // Effect untuk update stats dengan prefix handling
  useEffect(() => {
    if (sidebarData.selectedGrid) {
      const gridData = sidebarData.selectedGrid.data || [];
      
      let burungnesiaCount = 0;
      let kupunesiaCount = 0;
      let fobiCount = 0;
      let taxaCount = 0;
      const uniqueSpecies = new Set();
      
      gridData.forEach(item => {
        const id = item.id || '';
        if (id.startsWith('brn_') || item.source?.includes('burungnesia')) burungnesiaCount++;
        else if (id.startsWith('kpn_') || item.source?.includes('kupunesia')) kupunesiaCount++;
        else if (id.startsWith('fob_') || item.source?.includes('fobi')) fobiCount++;
        else if (item.source?.includes('taxa')) taxaCount++;
      });

      sidebarData.species.forEach(species => {
        uniqueSpecies.add(`${species.nameLat}_${species.nameId}`);
      });

      setStats({
        burungnesia: burungnesiaCount,
        kupunesia: kupunesiaCount,
        fobi: fobiCount,
        observasi: burungnesiaCount + kupunesiaCount + fobiCount + taxaCount,
        spesies: uniqueSpecies.size,
        kontributor: gridData.length
      });
    }
  }, [sidebarData.selectedGrid, sidebarData.species, setStats]);

  // Tambahkan initial load effect
  useEffect(() => {
    if (mapRef.current && mapMarkers && !gridData.tiles.length) {
      const map = mapRef.current;
      const currentZoom = map.getZoom();
      const currentBounds = map.getBounds();
      
      if (currentBounds && currentBounds._southWest && currentBounds._northEast) {
        const filteredMarkers = filterMarkers(mapMarkers, filterParams, searchParams);
        updateGridData(filteredMarkers, currentZoom, currentBounds);
      }
    }
  }, [mapRef.current, mapMarkers]);

  const handleMapCreated = useCallback((map) => {
    mapRef.current = map;
    
    // Trigger immediate update after map creation
    const currentZoom = map.getZoom();
    const currentBounds = map.getBounds();
    
    if (currentBounds && currentBounds._southWest && currentBounds._northEast) {
      setZoomLevel(currentZoom);
      setVisibleBounds(currentBounds);
      
      if (mapMarkers) {
        const filteredMarkers = filterMarkers(mapMarkers, filterParams, searchParams);
        updateGridData(filteredMarkers, currentZoom, currentBounds);
      }
    }
  }, [mapMarkers, filterParams, searchParams, updateGridData]);

  // Effect untuk mengatur view peta saat ada filter lokasi
  useEffect(() => {
    if (mapRef.current && searchParams?.latitude && searchParams?.longitude) {
      const map = mapRef.current;
      
      if (searchParams.boundingbox) {
        // Jika ada bounding box, gunakan fitBounds
        const bounds = [
          [searchParams.boundingbox[0], searchParams.boundingbox[2]],
          [searchParams.boundingbox[1], searchParams.boundingbox[3]]
        ];
        map.fitBounds(bounds, {
          padding: [50, 50],
          maxZoom: 12,
          animate: true,
          duration: 1 // durasi animasi dalam detik
        });
      } else {
        // Jika hanya ada koordinat, gunakan setView
        const zoom = filterParams?.radius 
          ? calculateZoomFromRadius(filterParams.radius)
          : 12;

        map.setView(
          [searchParams.latitude, searchParams.longitude],
          zoom,
          {
            animate: true,
            duration: 1
          }
        );
      }
    }
  }, [searchParams?.latitude, searchParams?.longitude, searchParams?.boundingbox, filterParams?.radius]);

  // Helper function untuk menghitung zoom level berdasarkan radius
  const calculateZoomFromRadius = (radius) => {
    // Konversi radius (km) ke zoom level
    // Semakin kecil radius, semakin besar zoom level
    if (radius <= 1) return 15;
    if (radius <= 5) return 13;
    if (radius <= 10) return 12;
    if (radius <= 20) return 11;
    if (radius <= 50) return 10;
    if (radius <= 100) return 9;
    return 8;
  };

  const handleReset = useCallback(() => {
    // Reset map view ke default
    if (mapRef.current) {
      const map = mapRef.current;
      map.setView(
        defaultMapConfig.center,
        defaultMapConfig.zoom,
        { animate: true }
      );

      // Update grid dengan data tanpa filter
      const currentBounds = map.getBounds();
      if (mapMarkers) {
        // Reset filter dan update grid
        updateGridData(mapMarkers, map.getZoom(), currentBounds);
      }
    }
    
    // Trigger parent reset untuk menghapus filter dan search
    if (onReset) {
      onReset();
    }
  }, [mapMarkers, updateGridData, onReset]);

  // Tambahkan effect untuk memantau pembaruan
  useEffect(() => {
    if (lastUpdate) {
      // Perbarui grid data ketika ada pembaruan markers
      const currentBounds = mapRef.current?.getBounds();
      const currentZoom = mapRef.current?.getZoom();
      
      if (currentBounds && currentZoom) {
        const filteredMarkers = filterMarkers(mapMarkers, filterParams, searchParams);
        updateGridData(filteredMarkers, currentZoom, currentBounds);
      }
    }
  }, [lastUpdate, mapMarkers]);

  if (loading) {
    return (
      <div className="flex justify-center items-center h-[70vh] bg-gray-50">
        <div className="flex items-center space-x-2">
          <div className="w-4 h-4 border-2 border-teal-600 border-t-transparent rounded-full animate-spin"></div>
          <span className="text-gray-600">Memuat peta...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col md:flex-row min-h-screen">
      <div className="flex-1 relative overflow-hidden">
        <MapContainer
          {...defaultMapConfig}
          className="absolute inset-0"
          scrollWheelZoom={isMobile}
          whenCreated={handleMapCreated}
          ref={mapRef}
        >
          <TileLayer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          />
          
          <MapController 
            setVisibleBounds={setVisibleBounds}
            setZoomLevel={setZoomLevel}
            setVisibleGrid={setVisibleGrid}
          />

<ZoomHandler 
          gridData={gridData}
          setVisibleGrid={setVisibleGrid}
          isMobile={isMobile}
        />

        <MapControls onReset={handleReset} />


          <MapOverlay
            grid={gridData.tiles}
            markers={mapMarkers}
            bounds={visibleBounds}
            zoomLevel={zoomLevel}
            onGridClick={toggleSidebar}
            filterParams={filterParams}
            searchParams={searchParams}
          />
        </MapContainer>

        {isLoading && (
          <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-[1000]">
            <div className="bg-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-3">
              <div className="w-5 h-5 border-3 border-teal-600 border-t-transparent rounded-full animate-spin"></div>
              <span className="text-gray-700 text-sm">Memuat data peta...</span>
            </div>
          </div>
        )}
      </div>
      {sidebarData.isOpen && (
        <Sidebar
          data={sidebarData}
          onClose={() => toggleSidebar()}
          onLoadMore={loadMore}
        />
      )}

    </div>
  );
};

export default React.memo(MapView);
