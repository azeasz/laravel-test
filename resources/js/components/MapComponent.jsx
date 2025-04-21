import React, { useEffect, useState, useRef, useCallback } from 'react';
import { MapContainer, TileLayer, Rectangle, Marker, ScaleControl, useMap } from 'react-leaflet';
import L from 'leaflet';
import axios from 'axios';
import 'leaflet/dist/leaflet.css';

const MapComponent = () => {
  const [gridData, setGridData] = useState({
    small: [],
    medium: [],
    large: [],
    extraLarge: []
  });
  const [visibleGrid, setVisibleGrid] = useState('extraLarge');
  const [loading, setLoading] = useState(true);
  const [selectedGridData, setSelectedGridData] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [showSidebar, setShowSidebar] = useState(false);
  const itemsPerPage = 7;
  const sidebarRef = useRef();

  const redCircleIcon = new L.DivIcon({
    className: 'custom-div-icon',
    html: "<div style='background-color:red; width:10px; height:10px; border-radius:50%;'></div>",
    iconSize: [10, 10]
  });

  useEffect(() => {
    const fetchMarkers = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/markers'); // Ganti dengan URL API Anda
        const checklists = response.data;
        const smallGrid = generateGrid(checklists, 0.02);
        const mediumGrid = generateGrid(checklists, 0.05);
        const largeGrid = generateGrid(checklists, 0.2);
        const extraLargeGrid = generateGrid(checklists, 0.5);

        setGridData({
          small: smallGrid,
          medium: mediumGrid,
          large: largeGrid,
          extraLarge: extraLargeGrid
        });
      } catch (error) {
        console.error('Error fetching markers:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchMarkers();
  }, []);

  const generateGrid = (checklists, gridSize) => {
    const grid = {};
    checklists.forEach(({ latitude, longitude, source, id, created_at }) => {
      const lat = Math.floor(latitude / gridSize) * gridSize;
      const lng = Math.floor(longitude / gridSize) * gridSize;
      const key = `${lat},${lng}`;

      if (!grid[key]) {
        grid[key] = { count: 0, source, data: [] };
      }
      grid[key].count++;
      grid[key].data.push({ id, latitude, longitude, source, created_at });
    });

    return Object.keys(grid).map(key => {
      const [lat, lng] = key.split(',').map(Number);
      return {
        bounds: [
          [lat, lng],
          [lat + gridSize, lng + gridSize]
        ],
        count: grid[key].count,
        source: grid[key].source,
        data: grid[key].data
      };
    });
  };

  const getColor = (count, source) => {
    if (source === 'kupunesia') {
      return 'rgba(128, 0, 128, 0.5)'; // Warna ungu untuk kupunesia
    }
    return count > 50 ? 'rgba(128, 0, 38, 0.5)' :
           count > 20 ? 'rgba(189, 0, 38, 0.5)' :
           count > 10 ? 'rgba(227, 26, 28, 0.5)' :
           count > 5  ? 'rgba(252, 78, 42, 0.5)' :
           count > 2  ? 'rgba(253, 141, 60, 0.5)' :
                        'rgba(254, 180, 76, 0.5)';
  };

  const handleGridClick = (grid) => {
    const sortedData = grid.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    setSelectedGridData(sortedData);
    setCurrentPage(1);
    setShowSidebar(true);
  };

  const ZoomHandler = () => {
    const map = useMap();
    useEffect(() => {
      const handleZoom = () => {
        const zoomLevel = map.getZoom();
        if (zoomLevel > 11) {
          if (visibleGrid !== 'small') {
            setVisibleGrid('small');
            setShowSidebar(false);
          }
        } else if (zoomLevel >= 10) {
          if (visibleGrid !== 'medium') {
            setVisibleGrid('medium');
          }
        } else if (zoomLevel >= 8) {
          if (visibleGrid !== 'large') {
            setVisibleGrid('large');
          }
        } else {
          if (visibleGrid !== 'extraLarge') {
            setVisibleGrid('extraLarge');
          }
        }
      };

      const handleMove = () => {
        if (visibleGrid === 'small') {
          const bounds = map.getBounds();
          const visibleMarkers = gridData.small.flatMap(grid =>
            grid.data.filter(item =>
              bounds.contains([item.latitude, item.longitude])
            )
          );
          setSelectedGridData(visibleMarkers);
        }
      };

      map.on('zoomend', handleZoom);
      map.on('moveend', handleMove);
      handleZoom(); // Initial check
      handleMove(); // Initial check
      return () => {
        map.off('zoomend', handleZoom);
        map.off('moveend', handleMove);
      };
    }, [map, visibleGrid, gridData.small]);

    return null;
  };

  const loadMoreData = useCallback(() => {
    setCurrentPage(prev => prev + 1);
  }, []);

  const handleScroll = () => {
    if (sidebarRef.current) {
      const { scrollTop, scrollHeight, clientHeight } = sidebarRef.current;
      if (scrollTop + clientHeight >= scrollHeight - 5) {
        loadMoreData();
      }
    }
  };

  const paginatedData = selectedGridData.slice(0, currentPage * itemsPerPage);

  return (
    <div style={{ display: 'flex' }}>
      <MapContainer
        center={[-2.5489, 118.0149]}
        zoom={5}
        style={{ height: '100vh', width: showSidebar ? '70%' : '100%' }}
      >
        <TileLayer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        />
        <ScaleControl position="bottomleft" />
        <ZoomHandler />
        {visibleGrid === 'small' && selectedGridData.map((item, index) => (
          <Marker
            key={`marker-${index}`}
            position={[item.latitude, item.longitude]}
            icon={redCircleIcon}
          />
        ))}
        {visibleGrid !== 'small' && gridData[visibleGrid].map((grid, index) => (
          <Rectangle
            key={`${visibleGrid}-${index}`}
            bounds={grid.bounds}
            color={getColor(grid.count, grid.source)}
            fillColor={getColor(grid.count, grid.source)}
            fillOpacity={0.5}
            weight={1}
            eventHandlers={{
              click: () => handleGridClick(grid),
            }}
          />
        ))}
      </MapContainer>
      {showSidebar && (
        <div
          ref={sidebarRef}
          onScroll={handleScroll}
          style={{
            width: '30%',
            height: '100vh',
            overflowY: 'auto',
            backgroundColor: '#f0f0f0',
            padding: '10px',
            boxSizing: 'border-box',
            position: 'relative'
          }}
        >
          <button
            onClick={() => setShowSidebar(false)}
            style={{
              position: 'absolute',
              top: '10px',
              right: '10px',
              zIndex: 1000
            }}
          >
            Close
          </button>
          {paginatedData.map((item, index) => (
            <div key={index} style={{ padding: '10px', borderBottom: '1px solid #ccc' }}>
              <p>ID: {item.id}</p>
              <p>Source: {item.source}</p>
              <p>Date: {new Date(item.created_at).toLocaleDateString()}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default MapComponent;
