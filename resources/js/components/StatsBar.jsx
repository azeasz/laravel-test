import React, { useEffect, useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faArrowRight } from '@fortawesome/free-solid-svg-icons';
import './StatsBar.css';
import { Inertia } from '@inertiajs/inertia';

const StatsBar = () => {
  const [stats, setStats] = useState({
    observasi: 0,
    burungnesia: 0,
    kupunesia: 0,
    fotoAudio: 0,
    spesies: 0,
    kontributor: 0,
  });

  useEffect(() => {
    const token = localStorage.getItem('jwt_token'); // Ambil token JWT dari localStorage

    const fetchStats = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/burungnesia-count', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });
        const data = await response.json();
        setStats(prevStats => ({
          ...prevStats,
          observasi: data.observasi,
          fotoAudio: data.fotoAudio,
          spesies: data.spesies,
          kontributor: data.kontributor,
        }));
      } catch (error) {
        console.error('Error fetching stats data:', error);
      }
    };

    const fetchBurungnesia = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/burungnesia-count', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });
        const data = await response.json();
        setStats(prevStats => ({
          ...prevStats,
          burungnesia: data.count,
        }));
      } catch (error) {
        console.error('Error fetching burungnesia data:', error);
      }
    };

    const fetchKupunesia = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/kupunesia-count', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });
        const data = await response.json();
        setStats(prevStats => ({
          ...prevStats,
          kupunesia: data.count,
        }));
      } catch (error) {
        console.error('Error fetching kupunesia data:', error);
      }
    };

    fetchStats();
    fetchBurungnesia();
    fetchKupunesia();
  }, []);

  return (
    <div className="stats-bar">
      <div className="search-section">
        <input type="text" placeholder="Spesies/ genus/ famili" />
        <input type="text" placeholder="Lokasi" />
        <button className="btn-filter"><FontAwesomeIcon icon={faArrowRight} /></button>
        <button className="btn-filter">Filter</button>
      </div>
      <div className="stats-section">
        <div className="stat-item">
          <span className="stat-number">{stats.observasi}</span>
          <span className="stat-label">OBSERVASI</span>
        </div>
        <div className="stat-item">
          <span className="stat-number">{stats.burungnesia}</span>
          <span className="stat-label">BURUNGNESIA</span>
        </div>
        <div className="stat-item">
          <span className="stat-number">{stats.kupunesia}</span>
          <span className="stat-label">KUPUNESIA</span>
        </div>
        <div className="stat-item">
          <span className="stat-number">{stats.fotoAudio}</span>
          <span className="stat-label">FOTO & AUDIO</span>
        </div>
        <div className="stat-item">
          <span className="stat-number">{stats.spesies}</span>
          <span className="stat-label">SPESIES (Tree)</span>
        </div>
        <div className="stat-item">
          <span className="stat-number">{stats.kontributor}</span>
          <span className="stat-label">KONTRIBUTOR</span>
        </div>
      </div>
    </div>
  );
};

export default StatsBar;