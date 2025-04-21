import React, { useState, useEffect, useRef } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faArrowRight, faFilter, faTimes, faSearch, faMapMarkerAlt } from '@fortawesome/free-solid-svg-icons';
import CountUp from 'react-countup';
import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import PropTypes from 'prop-types';
import { calculateDistance } from '../../utils/geoHelpers';

const StatsBar = ({ stats, onSearch }) => {
  const safeStats = {
    observasi: stats?.observasi || 0,
    burungnesia: stats?.burungnesia || 0,
    kupunesia: stats?.kupunesia || 0,
    fobi: stats?.fobi || 0,
    fotoAudio: stats?.fotoAudio || 0,
    spesies: stats?.spesies || 0,
    kontributor: stats?.kontributor || 0,
  };

  const statsData = [
    { label: 'OBSERVASI', value: safeStats.observasi, duration: 0.5 },
    { label: 'BURUNGNESIA', value: safeStats.burungnesia, duration: 1.5 },
    { label: 'KUPUNESIA', value: safeStats.kupunesia, duration: 0.5 },
    // { label: 'FOTO & AUDIO', value: safeStats.fotoAudio, duration: 0.5 },
    { label: 'SPESIES', value: safeStats.spesies, duration: 0.5 },
    { label: 'KONTRIBUTOR', value: safeStats.kontributor, duration: 0.5 },
  ];

  const [searchParams, setSearchParams] = useState({
    search: '',
    location: '',
    latitude: '',
    longitude: '',
    searchType: 'all'
  });

  const [filterParams, setFilterParams] = useState({
    start_date: '',
    end_date: '',
    grade: [],
    has_media: false,
    media_type: '',
    data_source: ['fobi', 'burungnesia', 'kupunesia']
  });

  const [showFilter, setShowFilter] = useState(false);
  const [locationSuggestions, setLocationSuggestions] = useState([]);
  const [isLoadingLocation, setIsLoadingLocation] = useState(false);
  const [speciesSuggestions, setSpeciesSuggestions] = useState([]);
  const [isLoadingSpecies, setIsLoadingSpecies] = useState(false);

  const suggestionRef = useRef(null);
  const locationSuggestionRef = useRef(null);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (suggestionRef.current && !suggestionRef.current.contains(event.target)) {
        setSpeciesSuggestions([]);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (locationSuggestionRef.current && !locationSuggestionRef.current.contains(event.target)) {
        setLocationSuggestions([]);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  useEffect(() => {
    const debounceTimer = setTimeout(() => {
      if (searchParams.location) {
        handleLocationSearch(searchParams.location);
      }
    }, 500);

    return () => clearTimeout(debounceTimer);
  }, [searchParams.location]);

  useEffect(() => {
    const debounceTimer = setTimeout(() => {
      if (searchParams.search) {
        handleSpeciesSearch(searchParams.search);
      } else {
        setSpeciesSuggestions([]);
      }
    }, 500);

    return () => clearTimeout(debounceTimer);
  }, [searchParams.search]);

  const handleLocationSearch = async (locationName) => {
    if (!locationName) {
      setLocationSuggestions([]);
      return;
    }

    setIsLoadingLocation(true);
    try {
      // Batas koordinat Indonesia
      const viewbox = {
        minLon: 95.0,  // Batas barat
        maxLon: 141.0, // Batas timur
        minLat: -11.0, // Batas selatan
        maxLat: 6.0,   // Batas utara
      };

      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?` +
        `format=json&` +
        `q=${encodeURIComponent(locationName)}&` +
        `limit=5&` +
        `addressdetails=1&` +
        `bounded=1&` +
        `countrycodes=id&` +
        `viewbox=${viewbox.minLon},${viewbox.minLat},${viewbox.maxLon},${viewbox.maxLat}&` +
        `bounded=1`
      );
      const data = await response.json();

      // Filter dan urutkan hasil berdasarkan relevansi dan tipe area
      const filteredData = data
        .filter(item => {
          const lat = parseFloat(item.lat);
          const lon = parseFloat(item.lon);
          return (
            lat >= viewbox.minLat &&
            lat <= viewbox.maxLat &&
            lon >= viewbox.minLon &&
            lon <= viewbox.maxLon
          );
        })
        .map(item => {
          // Hitung radius berdasarkan bounding box
          const bbox = item.boundingbox.map(coord => parseFloat(coord));
          const areaWidth = calculateDistance(bbox[0], bbox[2], bbox[0], bbox[3]);
          const areaHeight = calculateDistance(bbox[0], bbox[2], bbox[1], bbox[2]);
          const radius = Math.max(areaWidth, areaHeight) / 2;

          // Tentukan tipe area dan sesuaikan radius minimum
          let minRadius = 1; // km
          if (item.type === 'administrative') {
            if (item.address?.state) minRadius = 5;
            if (item.address?.county) minRadius = 10;
            if (item.address?.region) minRadius = 50;
            if (item.address?.province) minRadius = 100;
          }

          return {
            ...item,
            calculatedRadius: Math.max(radius, minRadius)
          };
        })
        .sort((a, b) => {
          // Prioritaskan hasil berdasarkan tipe dan importance
          if (a.type === 'administrative' && b.type !== 'administrative') return -1;
          if (b.type === 'administrative' && a.type !== 'administrative') return 1;
          return b.importance - a.importance;
        });

      setLocationSuggestions(filteredData.map(item => ({
        display_name: item.display_name
          .replace(', Indonesia', '')
          .replace(/,([^,]*)$/, ''),
        lat: item.lat,
        lon: item.lon,
        boundingbox: item.boundingbox,
        type: item.type,
        importance: item.importance,
        address: item.address,
        radius: item.calculatedRadius
      })));
    } catch (error) {
      console.error('Error searching location:', error);
    } finally {
      setIsLoadingLocation(false);
    }
  };

  const handleLocationSelect = (location) => {
    const lat = parseFloat(location.lat).toFixed(6);
    const lon = parseFloat(location.lon).toFixed(6);
    const bbox = location.boundingbox.map(coord => parseFloat(coord));

    setSearchParams({
      ...searchParams,
      location: location.display_name,
      latitude: lat,
      longitude: lon,
      radius: location.radius,
      boundingbox: bbox
    });

    setLocationSuggestions([]);
  };

  const handleSearch = () => {
    console.log('Search params before combining:', searchParams);
    onSearch(searchParams);
    setShowFilter(false);
  };

  const handleApplyFilter = () => {
    if (typeof onSearch === 'function') {
      const formattedParams = {
        ...searchParams,
        ...filterParams,
        grade: filterParams.grade
      };
      onSearch(formattedParams);
    }
    setShowFilter(false);
  };

  const handleResetFilter = () => {
    setFilterParams({
      start_date: '',
      end_date: '',
      grade: [],
      has_media: false,
      media_type: '',
      data_source: ['fobi', 'burungnesia', 'kupunesia']
    });
    setSearchParams({
      search: searchParams.search,
      location: '',
      latitude: '',
      longitude: '',
      searchType: 'all'
    });
  };

  const handleSpeciesSearch = async (query) => {
    if (!query) return;

    setIsLoadingSpecies(true);
    try {
      const response = await fetch(`${import.meta.env.VITE_API_URL}/species-suggestions?query=${encodeURIComponent(query)}`);
      const data = await response.json();
      
      // Proses dan urutkan data berdasarkan level taksonomi
      const processedData = data
        .map(item => ({
          ...item,
          scientific_name: item.scientific_name.replace(/\s+(?:[A-Z][a-z]+(?:\s*&\s*[A-Z][a-z]+)?(?:,\s*\d{4}[a-z]?)?|\([^)]+\))/g, '').trim(),
          display_name: item.display_name.replace(/\s+(?:[A-Z][a-z]+(?:\s*&\s*[A-Z][a-z]+)?(?:,\s*\d{4}[a-z]?)?|\([^)]+\))/g, '').trim(),
          taxonomicLevel: item.rank || (
            item.family ? 'family' :
            item.genus ? 'genus' : 'species'
          )
        }))
        .sort((a, b) => {
          // Urutkan berdasarkan level taksonomi
          const levelOrder = { family: 1, genus: 2, species: 3 };
          if (levelOrder[a.taxonomicLevel] !== levelOrder[b.taxonomicLevel]) {
            return levelOrder[a.taxonomicLevel] - levelOrder[b.taxonomicLevel];
          }
          // Jika level sama, urutkan berdasarkan nama
          return a.scientific_name.localeCompare(b.scientific_name);
        });

      setSpeciesSuggestions(processedData);
    } catch (error) {
      console.error('Error searching species:', error);
    } finally {
      setIsLoadingSpecies(false);
    }
  };

  return (
    <div className="flex flex-col items-center bg-[#5f8b8b] mt-10 md:mt-12 md:p-0 text-white w-full md:flex-row md:justify-between">
      <div className="hidden md:flex flex-col items-center w-full md:flex-row md:justify-start md:w-auto mt-2">
        <div className="relative w-full md:w-60 md:mr-2">
          <input
            type="text"
            placeholder="Spesies/ genus/ famili"
            value={searchParams.search}
            onChange={(e) => setSearchParams({...searchParams, search: e.target.value})}
            className="m-2 p-2 w-full md:w-60 border-none text-sm rounded text-black"
          />
          <FontAwesomeIcon icon={faSearch} className="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400" />

          {speciesSuggestions.length > 0 && (
            <div ref={suggestionRef} className="absolute z-50 w-full bg-white mt-1 rounded shadow-lg">
              {speciesSuggestions.map((suggestion, index) => (
                <div
                  key={index}
                  className="p-2 hover:bg-gray-100 cursor-pointer text-gray-700 text-sm"
                  onClick={() => {
                    setSearchParams({...searchParams, search: suggestion.scientific_name});
                    setSpeciesSuggestions([]);
                  }}
                >
                  <div className="font-medium">{suggestion.display_name}</div>
                  {suggestion.family && (
                    <div className="text-xs text-gray-500">
                      {suggestion.family}
                      {suggestion.common_name && ` | ${suggestion.common_name}`}
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}
          {isLoadingSpecies && (
            <div className="absolute z-50 w-full bg-white mt-1 rounded shadow-lg p-2 text-gray-700 text-sm">
              Mencari...
            </div>
          )}
        </div>

        <div className="relative w-full md:w-60 md:mr-5">
          <input
            type="text"
            placeholder="Lokasi"
            value={searchParams.location}
            onChange={(e) => setSearchParams({...searchParams, location: e.target.value})}
            className="m-2 p-2 w-full md:w-60 border-none text-sm rounded text-black"
          />
          <FontAwesomeIcon
            icon={faMapMarkerAlt}
            className="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"
          />

          {locationSuggestions.length > 0 && (
            <div ref={locationSuggestionRef} className="absolute z-50 w-full bg-white mt-1 rounded shadow-lg">
              {locationSuggestions.map((location, index) => (
                <div
                  key={index}
                  className="p-2 hover:bg-gray-100 cursor-pointer text-gray-700 text-sm"
                  onClick={() => handleLocationSelect(location)}
                >
                  {location.display_name}
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="flex space-x-2 mt-0 md:mt-0">
          <button
            onClick={handleSearch}
            className="bg-[#f0c040] border-none p-2 px-4 cursor-pointer rounded hover:bg-[#e0b030]"
          >
            <FontAwesomeIcon icon={faArrowRight} />
          </button>
          <button
            onClick={() => setShowFilter(!showFilter)}
            className="bg-[#f0c040] border-none p-2 px-4 cursor-pointer rounded hover:bg-[#e0b030]"
          >
            <FontAwesomeIcon icon={showFilter ? faTimes : faFilter} />
          </button>
        </div>
      </div>

      {showFilter && (
        <div className="filter-panel absolute top-full left-0 bg-white p-4 shadow-lg z-50 w-full md:w-80 rounded-b">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-gray-800 font-bold">Filter</h3>
            <button
              onClick={handleResetFilter}
              className="text-sm text-gray-500 hover:text-gray-700"
            >
              Reset
            </button>
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2 text-sm font-medium">Sumber Data</label>
            <div className="space-y-2">
              {['fobi', 'burungnesia', 'kupunesia'].map((source) => (
                <label key={source} className="flex items-center">
                  <input
                    type="checkbox"
                    checked={filterParams.data_source.includes(source)}
                    onChange={(e) => {
                      const newSources = e.target.checked
                        ? [...filterParams.data_source, source]
                        : filterParams.data_source.filter(s => s !== source);
                      if (newSources.length > 0) {
                        setFilterParams({...filterParams, data_source: newSources});
                      }
                    }}
                    className="mr-2"
                  />
                  <span className="text-sm text-gray-700 capitalize">{source}</span>
                </label>
              ))}
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2 text-sm font-medium">Radius Pencarian (km)</label>
            <input
              type="number"
              value={filterParams.radius}
              onChange={(e) => setFilterParams({...filterParams, radius: e.target.value})}
              className="w-full p-2 border rounded"
              min="1"
              max="100"
              style={{ color: 'black' }}
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2 text-sm font-medium">Tanggal Mulai</label>
            <input
              type="date"
              value={filterParams.start_date}
              onChange={(e) => setFilterParams({...filterParams, start_date: e.target.value})}
              className="w-full p-2 border rounded"
              style={{ color: 'black' }}
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2 text-sm font-medium">Tanggal Akhir</label>
            <input
              type="date"
              value={filterParams.end_date}
              onChange={(e) => setFilterParams({...filterParams, end_date: e.target.value})}
              className="w-full p-2 border rounded"
              style={{ color: 'black' }}
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2 text-sm font-medium">Grade</label>
            <div className="space-y-2">
              {[
                { value: 'research grade', label: 'ID Lengkap' },
                { value: 'needs id', label: 'Bantu Iden' },
                { value: 'low quality id', label: 'ID Kurang' },
                { value: 'casual', label: 'Casual' }
              ].map((option) => (
                <label key={option.value} className="flex items-center">
                  <input
                    type="checkbox"
                    checked={filterParams.grade.includes(option.value)}
                    onChange={(e) => {
                      const newGrades = e.target.checked
                        ? [...filterParams.grade, option.value]
                        : filterParams.grade.filter(g => g !== option.value);
                      setFilterParams({...filterParams, grade: newGrades});
                    }}
                    className="mr-2"
                  />
                  <span className="text-sm text-gray-700">{option.label}</span>
                </label>
              ))}
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2 text-sm font-medium">Tipe Media</label>
            <select
              value={filterParams.media_type}
              onChange={(e) => setFilterParams({...filterParams, media_type: e.target.value})}
              className="w-full p-2 border rounded"
              style={{ color: 'black' }}
            >
              <option value="">Semua Media</option>
              <option value="photo">Foto</option>
              <option value="audio">Audio</option>
            </select>
          </div>

          <div className="mb-4">
            <label className="flex items-center text-sm text-gray-700">
              <input
                type="checkbox"
                checked={filterParams.has_media}
                onChange={(e) => setFilterParams({...filterParams, has_media: e.target.checked})}
                className="mr-2"
              />
              Hanya tampilkan dengan media
            </label>
          </div>

          <button
            onClick={handleApplyFilter}
            className="w-full bg-[#f0c040] text-white p-2 rounded hover:bg-[#e0b030]"
          >
            Terapkan Filter
          </button>
        </div>
      )}

      <div className="flex flex-wrap justify-center mt-5 md:mt-2 md:justify-end w-full">
        <div className="block md:hidden w-full px-4">
          <Swiper
            spaceBetween={10}
            slidesPerView={3}
            slidesPerGroup={3}
            loop={true}
            className="mySwiper"
            breakpoints={{
              320: { slidesPerView: 3, spaceBetween: 10 },
              480: { slidesPerView: 3, spaceBetween: 15 },
              640: { slidesPerView: 3, spaceBetween: 20 }
            }}
          >
            {statsData.map((stat, index) => (
              <SwiperSlide key={index} className="!w-1/3">
                <div className="text-center">
                  <small className="text-sm font-bold text-[#f0c040] block">
                    <CountUp end={stat.value} duration={stat.duration} />
                  </small>
                  <small className="block text-xs whitespace-nowrap">{stat.label}</small>
                </div>
              </SwiperSlide>
            ))}
          </Swiper>
        </div>
        <div className="hidden md:flex flex-wrap justify-center md:justify-end w-full">
          {statsData.map((stat, index) => (
            <div key={index} className="m-2 text-center w-1/3 md:w-auto">
              <small className="text-sm font-bold text-[#f0c040]">
                <CountUp end={stat.value} duration={stat.duration} />
              </small>
              <small className="block text-xs">{stat.label}</small>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

StatsBar.propTypes = {
  stats: PropTypes.shape({
    observasi: PropTypes.number,
    burungnesia: PropTypes.number,
    kupunesia: PropTypes.number,
    fobi: PropTypes.number,
    fotoAudio: PropTypes.number,
    spesies: PropTypes.number,
    kontributor: PropTypes.number,
  }).isRequired,
  onSearch: PropTypes.func.isRequired,
};

export default StatsBar;
