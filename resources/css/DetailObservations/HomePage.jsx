import React, { useState, useEffect, useCallback } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faThLarge, faThList, faMap } from '@fortawesome/free-solid-svg-icons';
import GridView from './GridView';
import ListView from './ListView';
import MapView from './MapView';
import StatsBar from './StatsBar';
import { apiFetch } from '../../utils/api';
import { debounce } from 'lodash';

const HomePage = ({ searchParams, filterParams, onSearch }) => {
  const [view, setView] = useState('map');
  const [loading, setLoading] = useState(false);
  const [markers, setMarkers] = useState([]);
  const [stats, setStats] = useState({
    burungnesia: 0,
    kupunesia: 0,
    fobi: 0,
    observasi: 0,
    spesies: 0,
    kontributor: 0,
  });

  // Fetch markers saat komponen mount
  useEffect(() => {
    const fetchMarkers = async () => {
      setLoading(true);
      try {
        const token = localStorage.getItem('jwt_token');
        const [burungnesiaRes, kupunesiaRes] = await Promise.all([
          apiFetch('/markers', {
            headers: { 'Authorization': `Bearer ${token}` },
          }),
          apiFetch('/fobi-markers', {
            headers: { 'Authorization': `Bearer ${token}` },
          })
        ]);

        const burungnesiaData = await burungnesiaRes.json();
        const kupunesiaData = await kupunesiaRes.json();

        // Gabungkan dan tambahkan source untuk setiap marker
        const combinedMarkers = [
          ...burungnesiaData.map(marker => ({ ...marker, source: 'burungnesia' })),
          ...kupunesiaData.map(marker => ({ ...marker, source: 'kupunesia' }))
        ];

        setMarkers(combinedMarkers);
      } catch (error) {
        console.error('Error fetching markers:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchMarkers();
  }, []);

  // Effect untuk update markers saat filter atau search berubah
  useEffect(() => {
    if (markers.length > 0) {
      const filteredMarkers = markers.filter(marker => {
        // Filter berdasarkan searchParams
        if (searchParams?.query && typeof searchParams.query === 'string' && searchParams.query.trim() !== '') {
          const searchLower = searchParams.query.toLowerCase();
          const matchesSearch = 
            (marker.name?.toLowerCase().includes(searchLower)) ||
            (marker.location?.toLowerCase().includes(searchLower));
          if (!matchesSearch) return false;
        }

        // Filter berdasarkan filterParams
        if (filterParams?.data_source?.length > 0) {
          const sourceMatch = filterParams.data_source.some(source => {
            if (source === 'burungnesia') {
              return marker.source === 'burungnesia';
            }
            if (source === 'kupunesia') {
              return marker.source === 'kupunesia';
            }
            if (source === 'fobi') {
              return marker.source === 'fobi';
            }
            return false;
          });
          if (!sourceMatch) return false;
        }

        return true;
      });

      setMarkers(filteredMarkers);
    }
  }, [searchParams, filterParams]);

  const debouncedSearch = useCallback(
    debounce((value) => {
      onSearch(value);
    }, 500),
    []
  );

  const fetchStats = async () => {
    const token = localStorage.getItem('jwt_token');
    const cachedStats = localStorage.getItem('cachedStats');

    if (cachedStats) {
      setStats(JSON.parse(cachedStats));
      return;
    }

    try {
      const [
        burungnesiaResponse,
        kupunesiaResponse,
        fobiResponse,
        totalSpeciesResponse,
        totalContributorsResponse
      ] = await Promise.all([
        apiFetch('/burungnesia-count', {
          headers: { 'Authorization': `Bearer ${token}` },
        }),
        apiFetch('/kupunesia-count', {
          headers: { 'Authorization': `Bearer ${token}` },
        }),
        apiFetch('/fobi-count', {
          headers: { 'Authorization': `Bearer ${token}` },
        }),
        apiFetch('/total-species'),
        apiFetch('/total-contributors')
      ]);

      const burungnesiaData = await burungnesiaResponse.json();
      const kupunesiaData = await kupunesiaResponse.json();
      const fobiData = await fobiResponse.json();
      const totalSpeciesData = await totalSpeciesResponse.json();
      const totalContributorsData = await totalContributorsResponse.json();

      const newStats = {
        burungnesia: burungnesiaData.burungnesiaCount,
        kupunesia: kupunesiaData.kupunesiaCount,
        fobi: fobiData.fobiCount,
        observasi: burungnesiaData.burungnesiaCount + kupunesiaData.kupunesiaCount + fobiData.fobiCount,
        spesies: totalSpeciesData.totalSpecies,
        kontributor: totalContributorsData.totalContributors,
        fotoAudio: 0
      };

      setStats(newStats);
      localStorage.setItem('cachedStats', JSON.stringify(newStats));
    } catch (error) {
      console.error('Error fetching stats:', error);
    }
  };

  useEffect(() => {
    fetchStats();
  }, []);

  useEffect(() => {
    if (searchParams) {
      console.log('Search params received in HomePage:', searchParams);
    }
  }, [searchParams]);

  const resetStats = () => {
    fetchStats();
  };


  const handleReset = useCallback(() => {
    // Reset search params
    if (onSearch) {
      onSearch({});
    }
    
    // Reset stats
    fetchStats();
  }, [onSearch, fetchStats]);

  return (
    <div>
      <div className="relative">
        <StatsBar
          stats={stats}
          onSearch={onSearch}
          searchParams={searchParams}
          filterParams={filterParams}
        />
        <div className="flex justify-center md:justify-end md:absolute md:right-4 md:top-30 space-x-1 bg-none p-1 cursor-pointer z-50 text-white">
          <button 
            onClick={() => { setView('map'); resetStats(); }} 
            className={`p-2 ${view === 'map' ? 'bg-[#4a7571]' : 'bg-[#679995]'} hover:bg-gray-300 shadow-inner`}
          >
            <FontAwesomeIcon icon={faMap} className="text-shadow-md" />
          </button>
          <button 
            onClick={() => { setView('grid'); resetStats(); }} 
            className={`p-2 ${view === 'grid' ? 'bg-[#4a7571]' : 'bg-[#679995]'} hover:bg-gray-300 shadow-inner`}
          >
            <FontAwesomeIcon icon={faThLarge} className="text-shadow-md" />
          </button>
          <button 
            onClick={() => { setView('list'); resetStats(); }} 
            className={`p-2 ${view === 'list' ? 'bg-[#4a7571]' : 'bg-[#679995]'} hover:bg-gray-300 shadow-inner`}
          >
            <FontAwesomeIcon icon={faThList} className="text-shadow-md" />
          </button>
        </div>
      </div>

      <div className="mt-0 relative">
        {loading && (
          <div className="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-[1000]">
            <div className="bg-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-3">
              <div className="w-5 h-5 border-3 border-teal-600 border-t-transparent rounded-full animate-spin"></div>
              <span className="text-gray-700 text-sm">Memuat data...</span>
            </div>
          </div>
        )}

        {view === 'grid' && (
          <GridView
            searchParams={searchParams}
            filterParams={filterParams}
          />
        )}
        {view === 'list' && (
          <ListView
            searchParams={searchParams}
            filterParams={filterParams}
          />
        )}
        {view === 'map' && (
          <MapView
            markers={markers}
            setStats={setStats}
            searchParams={searchParams}
            filterParams={filterParams}
            setLoading={setLoading}
            onReset={handleReset}
          />
        )}
      </div>
    </div>
  );
};

export default HomePage;
