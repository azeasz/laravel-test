import React, { useState, useEffect, useRef } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEye, faInfo, faListDots, faImage, faDove, faLocationDot, faQuestion, faCheck, faLink, faPlay, faPause, faUsers } from '@fortawesome/free-solid-svg-icons';
import 'swiper/css';
import './GridView.css';
import { useNavigate } from 'react-router-dom';

const SpectrogramPlayer = ({ audioUrl, spectrogramUrl }) => {
  const [isPlaying, setIsPlaying] = useState(false);
  const [progress, setProgress] = useState(0);
  const audioRef = useRef(null);

  const togglePlay = () => {
    if (audioRef.current) {
      if (isPlaying) {
        audioRef.current.pause();
      } else {
        audioRef.current.play();
      }
      setIsPlaying(!isPlaying);
    }
  };

  useEffect(() => {
    if (audioRef.current) {
      audioRef.current.addEventListener('timeupdate', () => {
        const duration = audioRef.current.duration;
        const currentTime = audioRef.current.currentTime;
        const progress = (currentTime / duration) * 100;
        setProgress(progress);
      });

      audioRef.current.addEventListener('ended', () => {
        setIsPlaying(false);
        setProgress(0);
      });
    }
  }, []);

  return (
    <div className="relative w-full h-full bg-black flex flex-col">
      <div className="relative flex-1 w-full h-32 bg-gray-900 overflow-hidden">
        <img
          src={spectrogramUrl}
          alt="Spectrogram"
          className="w-full h-full object-cover"
          loading="lazy"
        />
        {audioUrl && (
          <>
            <div className="absolute bottom-0 left-0 w-full h-0.5 bg-gray-700">
              <div
                className="h-full bg-emerald-500 transition-width duration-100"
                style={{ width: `${progress}%` }}
              />
            </div>
            <button
              onClick={togglePlay}
              className="absolute bottom-2 left-2 w-8 h-8 rounded-full bg-black/60 border border-white/20 text-white flex items-center justify-center cursor-pointer hover:bg-black/80 hover:scale-110 active:scale-95 transition-all duration-200"
              aria-label={isPlaying ? 'Pause' : 'Play'}
            >
              <FontAwesomeIcon
                icon={isPlaying ? faPause : faPlay}
                className="text-sm"
              />
            </button>
            <audio
              ref={audioRef}
              src={audioUrl}
              className="hidden"
              preload="metadata"
            />
          </>
        )}
      </div>
    </div>
  );
};

const MediaSlider = ({ images, spectrogram, audioUrl }) => {
  const [activeIndex, setActiveIndex] = useState(0);
  const mediaItems = [];

  if (images && images.length > 0) {
    images.forEach(img => {
      mediaItems.push({ type: 'image', url: img.url || img });
    });
  }

  if (spectrogram) {
    mediaItems.push({ type: 'spectrogram', url: spectrogram, audioUrl });
  }

  if (mediaItems.length === 0) {
    mediaItems.push({ type: 'image', url: '/default-image.jpg' });
  }

  return (
    <div className="relative">
      <div className="h-48 overflow-hidden bg-gray-900">
        {mediaItems[activeIndex]?.type === 'spectrogram' ? (
          <SpectrogramPlayer
            spectrogramUrl={mediaItems[activeIndex].url}
            audioUrl={mediaItems[activeIndex].audioUrl}
          />
        ) : (
          <img
            src={mediaItems[activeIndex].url}
            alt=""
            className="w-full h-full object-cover"
            loading="lazy"
          />
        )}
      </div>

      {mediaItems.length > 1 && (
        <div className="absolute bottom-2 left-0 right-0 flex justify-center gap-1 z-10">
          <div className="flex gap-1 px-2 py-1 rounded-full bg-black/30">
            {mediaItems.map((_, idx) => (
              <button
                key={idx}
                className={`w-2 h-2 rounded-full transition-colors ${
                  idx === activeIndex ? 'bg-white' : 'bg-gray-400 hover:bg-gray-300'
                }`}
                onClick={() => setActiveIndex(idx)}
              />
            ))}
          </div>
        </div>
      )}
    </div>
  );
};
const getGradeDisplay = (grade) => {
    switch(grade.toLowerCase()) {
      case 'research grade':
        return 'ID Lengkap';
      case 'needs id':
        return 'Bantu Iden';
      case 'casual':
        return 'Casual';
      default:
        return grade;
    }
  };


const Card = ({ item }) => {
  const navigate = useNavigate();

  const handleClick = (e) => {
    if (e.target.closest('.card-body') || e.target.closest('.card-footer')) {
      let path;
      switch(item.type) {
        case 'bird':
          path = `/burungnesia/observations/${item.id}`;
          break;
        case 'butterfly':
          path = `/kupunesia/observations/${item.id}`;
          break;
        default:
          path = `/observations/${item.id}`;
      }
      navigate(path);
    }
  };

  // Helper function untuk mendapatkan total count berdasarkan tipe
  const getTotalCount = () => {
    // Untuk FOBI
    if (item.type === 'general') {
      return {
        count: item.fobi_count || 0,
        label: 'FOBI',
        color: 'text-green-700'
      };
    }
    // Untuk Burungnesia
    else if (item.type === 'bird') {
      return [
        {
          count: item.fobi_count || 0,
          label: 'FOBI',
          color: 'text-green-700'
        },
        {
          count: item.burungnesia_count || 0,
          label: 'Burungnesia',
          color: 'text-blue-700'
        }
      ];
    }
    // Untuk Kupunesia
    else if (item.type === 'butterfly') {
      return [
        {
          count: item.fobi_count || 0,
          label: 'FOBI',
          color: 'text-green-700'
        },
        {
          count: item.kupunesia_count || 0,
          label: 'Kupunesia',
          color: 'text-purple-700'
        }
      ];
    }
    return null;
  };

  const totalCount = getTotalCount();

  return (
    <div className="card relative">
      <MediaSlider
        images={item.images || [item.image]}
        spectrogram={item.spectrogram}
        audioUrl={item.audioUrl}
      />

      <div className="card-body p-4 cursor-pointer hover:bg-gray-50" onClick={handleClick}>
        <div className="flex items-center justify-between mb-2">
          <span className="text-sm text-gray-600">{item.observer}</span>
          <span className={`px-2 py-1 rounded-full text-xs text-white ${
            item.quality.grade.toLowerCase() === 'research grade' ? 'bg-green-500' :
            item.quality.grade.toLowerCase() === 'needs id' ? 'bg-yellow-500' :
            'bg-gray-500'
          }`}>
            {getGradeDisplay(item.quality.grade)}
          </span>
        </div>
        <h5 className="font-medium text-lg mb-2">{item.title}</h5>
        <p className="text-sm text-gray-700 whitespace-pre-line">{item.description}</p>
      </div>

      <div className="card-footer p-4 bg-gray-50 cursor-pointer hover:bg-gray-100" onClick={handleClick}>
        <div className="flex items-center justify-between">
          <div className="quality-indicators flex gap-2 text-gray-600">
            {item.quality.has_media && <FontAwesomeIcon icon={faImage} title="Has Media" />}
            {item.quality.is_wild && <FontAwesomeIcon icon={faDove} title="Wild" />}
            {item.quality.location_accurate && <FontAwesomeIcon icon={faLocationDot} title="Location Accurate" />}
            {item.quality.needs_id && <FontAwesomeIcon icon={faQuestion} title="Needs ID" />}
            {item.type === 'general' && item.quality.recent_evidence && (
              <FontAwesomeIcon icon={faCheck} title="Recent Evidence" />
            )}
            {item.type === 'general' && item.quality.related_evidence && (
              <FontAwesomeIcon icon={faLink} title="Related Evidence" />
            )}
          </div>

          <div className="flex items-center gap-3 text-xs">
            {/* ID Count */}
            <div className="flex items-center gap-1 text-gray-600">
              <FontAwesomeIcon icon={faUsers} />
              <span>{item.identifications_count || 0}</span>
            </div>

            {/* Total Checklist Count */}
            {totalCount && (Array.isArray(totalCount) ? (
              <div className="flex items-center gap-2">
                {totalCount.map((count, idx) => (
                  <div key={idx} className={`flex items-center gap-1 ${count.color} font-medium`}>
                    <span>{count.count} {count.label}</span>
                  </div>
                ))}
              </div>
            ) : (
              <div className={`flex items-center gap-1 ${totalCount.color} font-medium`}>
                <span>{totalCount.count} {totalCount.label}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

const GridView = () => {
  const [visibleIndex, setVisibleIndex] = useState(null);
  const cardRefs = useRef([]);
  const [observations, setObservations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const toggleDescription = (index) => {
    setVisibleIndex(visibleIndex === index ? null : index);
  };

  const handleClickOutside = (event) => {
    if (cardRefs.current.every(ref => ref && !ref.contains(event.target))) {
      setVisibleIndex(null);
    }
  };

  const handleKeyDown = (event) => {
    if (event.key === 'Escape') {
      setVisibleIndex(null);
    }
  };

  useEffect(() => {
    document.addEventListener('mousedown', handleClickOutside);
    document.addEventListener('keydown', handleKeyDown);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
      document.removeEventListener('keydown', handleKeyDown);
    };
  }, []);

  useEffect(() => {
    const fetchObservations = async () => {
      try {
        setLoading(true);
        setError(null);

        console.log('Memulai fetch data...');

        const fetchPromises = [
          fetch('http://localhost:8000/api/general-observations')
            .then(res => res.json())
            .catch(err => {
              console.error('Error fetching general:', err);
              return { data: [] };
            }),
          fetch('http://localhost:8000/api/bird-observations')
            .then(res => res.json())
            .catch(err => {
              console.error('Error fetching birds:', err);
              return { data: [] };
            }),
          fetch('http://localhost:8000/api/butterfly-observations')
            .then(res => res.json())
            .catch(err => {
              console.error('Error fetching butterflies:', err);
              return { data: [] };
            })
        ];

        const [generalResponse, birdResponse, butterflyResponse] = await Promise.all(fetchPromises);

        console.log('Raw responses:', {
          general: generalResponse,
          birds: birdResponse,
          butterflies: butterflyResponse
        });

        const generalData = generalResponse?.data || [];
        const birdData = birdResponse?.data || [];
        const butterflyData = butterflyResponse?.data || [];

        const combinedData = [
          ...formatGeneralData(generalData),
          ...formatBirdData(birdData),
          ...formatButterflyData(butterflyData)
        ];

        console.log('Formatted data:', combinedData);

        if (combinedData.length === 0) {
          setError('Tidak ada data yang ditemukan. Silakan coba lagi nanti.');
          return;
        }

        setObservations(combinedData);
      } catch (err) {
        console.error('Error utama:', err);
        setError(`Gagal mengambil data: ${err.message || 'Unknown error'}`);
      } finally {
        setLoading(false);
      }
    };

    fetchObservations();
  }, []);

  // Fungsi helper untuk memformat data dengan pengecekan null/undefined
  const formatGeneralData = (data) => {
    if (!Array.isArray(data)) return [];
    return data.map(item => ({
      id: item?.id || '',
      taxa_id: item?.taxa_id || '',
      media_id: item?.media_id || '',
      image: item?.images?.[0]?.url || '/default-image.jpg',
      title: item?.scientific_name || 'Tidak ada nama',
      description: `Class: ${item?.class || '-'}
Order: ${item?.order || '-'}
Family: ${item?.family || '-'}
Genus: ${item?.genus || '-'}
Species: ${item?.species || '-'}
Details: ${item?.observation_details || '-'}`,
      observer: item?.observer_name || 'Anonymous',
      quality: {
        grade: item?.grade || 'casual',
        has_media: Boolean(item?.has_media),
        is_wild: Boolean(item?.is_wild),
        location_accurate: Boolean(item?.location_accurate),
        recent_evidence: Boolean(item?.recent_evidence),
        related_evidence: Boolean(item?.related_evidence),
        needs_id: Boolean(item?.needs_id),
        community_id_level: item?.community_id_level || null
      },
      created_at: item?.created_at || '',
      updated_at: item?.updated_at || '',
      type: 'general',
      spectrogram: item?.spectrogram || null,
      identifications_count: item?.identifications_count || 0,
      fobi_count: item?.fobi_count || 0,
    }));
  };

  const formatBirdData = (data) => {
    if (!Array.isArray(data)) return [];
    return data.map(item => ({
      id: item?.id || '',
      fauna_id: item?.fauna_id || '',
      image: item?.images?.[0]?.url || '/default-bird.jpg',
      title: item?.nameId || 'Tidak ada nama',
      description: `${item?.nameLat || '-'}\n${item?.family || '-'}\nGrade: ${item?.grade || '-'}\n${item?.notes || '-'}`,
      observer: item?.observer_name || 'Anonymous',
      count: `${item?.count || 0} Individu`,
      breeding: item?.breeding ? 'Breeding' : 'Non-breeding',
      breeding_note: item?.breeding_note || '-',
      quality: {
        grade: item?.grade || 'casual',
        has_media: Boolean(item?.has_media),
        is_wild: Boolean(item?.is_wild),
        location_accurate: Boolean(item?.location_accurate),
        needs_id: Boolean(item?.needs_id),
        community_level: item?.community_id_level || null
      },
      type: 'bird',
      spectrogram: item?.spectrogram || null,
      identifications_count: item?.identifications_count || 0,
      total_checklist: item?.burungnesia_count || 0,
    }));
  };

  const formatButterflyData = (data) => {
    if (!Array.isArray(data)) return [];
    return data.map(item => ({
      id: item?.id || '',
      fauna_id: item?.fauna_id || '',
      image: item?.images?.[0]?.url || '/default-butterfly.jpg',
      title: item?.nameId || 'Tidak ada nama',
      description: `${item?.nameLat || '-'}\n${item?.family || '-'}\nGrade: ${item?.grade || '-'}\n${item?.notes || '-'}`,
      observer: item?.observer_name || 'Anonymous',
      count: `${item?.count || 0} Individu`,
      breeding: item?.breeding ? 'Breeding' : 'Non-breeding',
      breeding_note: item?.breeding_note || '-',
      quality: {
        grade: item?.grade || 'casual',
        has_media: Boolean(item?.has_media),
        is_wild: Boolean(item?.is_wild),
        location_accurate: Boolean(item?.location_accurate),
        needs_id: Boolean(item?.needs_id),
        community_level: item?.community_id_level || null
      },
      type: 'butterfly',
      spectrogram: item?.spectrogram || null,
      identifications_count: item?.identifications_count || 0,
      total_checklist: item?.kupunesia_count || 0,
    }));
  };

  if (loading) return <div>Memuat data...</div>;
  if (error) return <div>{error}</div>;

  return (
    <>
      <div className="hidden md:grid grid-cols-5 gap-4">
        {observations.map((item, index) => (
          <Card key={index} item={item} />
        ))}
      </div>

      <div className="grid grid-cols-3 gap-4 md:hidden mx-1">
        {observations.map((item, index) => (
          <div key={index} className="card relative">
            <img src={item.image} alt={item.title} className="card-image h-24" />
            <span className="observer absolute w-full bottom-0 left-0 bg-black bg-opacity-50 text-white p-1 text-xs">
              {item.title}
            </span>
            <span className={`absolute top-0 left-0 text-xs px-2 py-1 text-white ${
            item.quality.grade.toLowerCase() === 'research grade' ? 'bg-green-500' :
            item.quality.grade.toLowerCase() === 'needs id' ? 'bg-yellow-500' :
            'bg-gray-500'
          }`}>
            {getGradeDisplay(item.quality.grade)}
          </span>
                      <button
              onClick={() => toggleDescription(index)}
              className="absolute top-0 right-0 bg-gray-800 text-white p-2 text-xs"
            >
              <FontAwesomeIcon icon={faInfo} />
            </button>
            {visibleIndex === index && (
              <div className="absolute bottom-0 right-0 bg-[#679995] bg-opacity-100 text-white p-1 text-xs">
                <p>{item.description}</p>
                <p>Observer: {item.observer}</p>
                {item.breeding && <p>{item.breeding}</p>}
                {item.count && <p>{item.count}</p>}
                {item.spectrogram && item.audioUrl && (
                  <SpectrogramPlayer
                    audioUrl={item.audioUrl}
                    spectrogramUrl={item.spectrogram}
                  />
                )}
              </div>
            )}
          </div>
        ))}
      </div>
    </>
  );
};

export default GridView;
