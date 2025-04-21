// src/App.js
import React, { useState, useEffect } from 'react';
import Header from './components/Header';
import Chart from 'chart.js/auto';

function App() {
  const [activityChart, setActivityChart] = useState(null);

  useEffect(() => {
    const ctx = document.getElementById('activityChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [],
        datasets: [
          {
            label: 'Observasi',
            data: [],
            borderColor: 'blue',
            borderWidth: 1,
            fill: true,
            backgroundColor: 'rgba(0, 0, 255, 0.1)',
            pointRadius: 0.5,
            pointHoverRadius: 10,
            pointHitRadius: 10,
          },
          {
            label: 'Identifikasi',
            data: [],
            borderColor: 'orange',
            borderWidth: 1,
            fill: true,
            backgroundColor: 'rgba(255, 165, 0, 0.1)',
            pointRadius: 0.5,
            pointHoverRadius: 10,
            pointHitRadius: 10,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            suggestedMax: 40,
          },
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function (context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.parsed.y !== null) {
                  label += context.parsed.y + ' observasi';
                }
                return label;
              },
            },
          },
        },
      },
    });

    setActivityChart(chart);
    updateChart('year', chart);

    return () => {
      chart.destroy();
    };
  }, []);

  const updateChart = (range, chart) => {
    let labels, dataObservasi, dataIdentifikasi;
    if (range === 'year') {
      labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      dataObservasi = [12, 19, 3, 5, 2, 3, 10, 15, 20, 25, 30, 35];
      dataIdentifikasi = [2, 3, 20, 5, 1, 4, 8, 12, 18, 22, 28, 32];
    } else if (range === 'month') {
      labels = Array.from({ length: 30 }, (_, i) => i + 1);
      dataObservasi = Array.from({ length: 30 }, () => Math.floor(Math.random() * 40));
      dataIdentifikasi = Array.from({ length: 30 }, () => Math.floor(Math.random() * 40));
    } else if (range === 'week') {
      labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
      dataObservasi = Array.from({ length: 7 }, () => Math.floor(Math.random() * 40));
      dataIdentifikasi = Array.from({ length: 7 }, () => Math.floor(Math.random() * 40));
    }

    chart.data.labels = labels;
    chart.data.datasets[0].data = dataObservasi;
    chart.data.datasets[1].data = dataIdentifikasi;
    chart.update();
  };

  return (
    <div className="App">
      <Header />
      <canvas id="activityChart" width="400" height="200"></canvas>
    </div>
  );
}

export default App;
