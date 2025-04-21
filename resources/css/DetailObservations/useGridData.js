import { useState, useEffect, useCallback, useRef } from 'react';
import localforage from 'localforage';
import { throttle } from 'lodash';

const GRID_SIZES = {
  extraLarge: 0.5,
  large: 0.2,
  medium: 0.05,
  small: 0.02
};

const THROTTLE_DELAY = 1000; // Naikkan ke 1 detik

const getGridSizeFromZoom = (zoom) => {
  if (zoom >= 10) return GRID_SIZES.small;
  if (zoom >= 8) return GRID_SIZES.medium;
  if (zoom >= 6) return GRID_SIZES.large;
  return GRID_SIZES.extraLarge;
};

export const useGridData = () => {
  const [gridData, setGridData] = useState({ tiles: [] });
  const gridWorker = useRef(null);
  const previousParams = useRef(null);

  useEffect(() => {
    gridWorker.current = new Worker(new URL('../workers/gridWorker.js', import.meta.url));
    return () => gridWorker.current?.terminate();
  }, []);

  const updateGridData = useCallback(async (markers, zoomLevel, bounds) => {
    if (!markers || !bounds || !gridWorker.current) return;

    const newParams = {
      zoom: zoomLevel,
      bounds: bounds.toBBoxString(),
      markersLength: markers.length
    };

    // Selalu update jika parameters berubah
    if (JSON.stringify(newParams) !== JSON.stringify(previousParams.current)) {
      previousParams.current = newParams;

      try {
        gridWorker.current.onmessage = async (e) => {
          const processedGrid = e.data;
          setGridData({ tiles: processedGrid });
        };

        gridWorker.current.postMessage({
          markers,
          gridSize: getGridSizeFromZoom(zoomLevel),
          bounds
        });
      } catch (error) {
        console.error('Error processing grid:', error);
      }
    }
  }, []);

  return { gridData, updateGridData };
}; 