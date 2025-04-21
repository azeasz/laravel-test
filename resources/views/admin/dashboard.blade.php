@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Statistik Cards Row -->
<div class="row g-2 mb-4">
    <!-- Taxa Card -->
    <div class="col-md-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total Taxa</h6>
                        <h3 class="card-title mb-0">{{ number_format($totalTaxa) }}</h3>
                    </div>
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-leaf fa-2x"></i>
                    </div>
                </div>
                {{-- <div class="mt-3">
                    <small class="text-muted">
                        {{ number_format($activeTaxa) }} rows
                    </small>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Identifications Card -->
    <div class="col-md-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total Identifikasi</h6>
                        <h3 class="card-title mb-0">{{ number_format($totalIdentifications) }}</h3>
                    </div>
                    <div class="stats-icon bg-success">
                        <i class="fas fa-microscope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Card -->
    <div class="col-md-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total Media</h6>
                        <h3 class="card-title mb-0">{{ number_format($totalMedia) }}</h3>
                    </div>
                    <div class="stats-icon bg-info">
                        <i class="fas fa-image fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overseers Card -->
    <div class="col-md-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total Observers</h6>
                        <h3 class="card-title mb-0">{{ number_format($totalOverseers) }}</h3>
                    </div>
                    <div class="stats-icon bg-info">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                {{-- <div class="mt-3">
                    <small class="text-muted">
                        {{ number_format($overseerGrowth, 1) }}% pertumbuhan bulan ini
                    </small>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Checklists Card -->
    <div class="col-md-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total Checklists</h6>
                        <h3 class="card-title mb-0">{{ number_format($totalChecklists) }}</h3>
                    </div>
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
                {{-- <div class="mt-3">
                    <small class="text-muted">
                        FOBI: {{ number_format($fobiChecklists) }}<br>
                        Burungnesia: {{ number_format($burungnesiaChecklists) }}<br>
                        Kupunesia: {{ number_format($kupunesiaChecklists) }}<br>
                        Completed: {{ number_format($completedChecklists) }}
                    </small>
                </div> --}}
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Checklist Activity Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aktivitas Checklist (7 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                <canvas id="checklistChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Region Distribution Chart -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Taxonomy</h5>
            </div>
            <div class="card-body">
                <canvas id="taxaDistributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Map Row -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Peta Sebaran</h5>
            </div>
            <div class="card-body">
                <div id="taxaMap" style="height: 500px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Activities and Top Observers Row -->
<div class="row g-3">
    <!-- Recent Activities -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($recentActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $activity['color'] }}">
                                <i class="fas {{ $activity['icon'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $activity['title'] }}</strong>
                                            <small class="text-muted ms-2">
                                                {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="mb-2">
                                            {{ $activity['description'] }}
                                            @if(isset($activity['user']))
                                                <br>
                                                <small class="text-muted">oleh {{ $activity['user'] }}</small>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(isset($activity['actions']))
                                                @foreach($activity['actions'] as $action)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ $action['url'] }}">
                                                            <i class="fas fa-{{ $action['icon'] }} me-2"></i>
                                                            {{ $action['label'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   onclick="loadActivityDetail('{{ $activity['type'] }}', {{ $activity['id'] }}, '{{ $activity['detail_url'] }}')">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Lihat Detail
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada aktivitas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


<!-- Top Observers Row -->
<div class="row g-3 mb-4">
    <!-- FOBI Top Observers -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top FOBI Observers</h5>
            </div>
            <div class="card-body">
                @if(count($topFobiObservers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($topFobiObservers as $observer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/profiles/' . $observer['profile_picture']) }}"
                                                     class="rounded-circle me-2"
                                                     width="32"
                                                     height="32"
                                                     alt="{{ $observer['name'] }}">
                                                <div>
                                                    <div>{{ $observer['name'] }}</div>
                                                    <small class="text-muted">{{ $observer['organization'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ number_format($observer['count']) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Belum ada data observer</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Burungnesia Top Observers -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Burungnesia Observers</h5>
            </div>
            <div class="card-body">
                @if(count($topBurungnesiaObservers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($topBurungnesiaObservers as $observer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/profiles/') }}"
                                                class="rounded-circle me-2"
                                                width="32"
                                                height="32"
                                                alt="{{ $observer['name'] }}">
                                                <div>
                                                    <div>{{ $observer['name'] }}</div>
                                                    <small class="text-muted">{{ $observer['organization'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-success">{{ number_format($observer['count']) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Belum ada data observer</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Kupunesia Top Observers -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Kupunesia Observers</h5>
            </div>
            <div class="card-body">
                @if(count($topKupunesiaObservers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($topKupunesiaObservers as $observer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/profiles/') }}"
                                                class="rounded-circle me-2"
                                                width="32"
                                                height="32"
                                                alt="{{ $observer['name'] }}">
                                                <div>
                                                    <div>{{ $observer['name'] }}</div>
                                                    <small class="text-muted">{{ $observer['organization'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-warning">{{ number_format($observer['count']) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Belum ada data observer</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Aktivitas -->
<div class="modal fade" id="activityDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="checklistDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Checklist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Taksonomi</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Nama Ilmiah</th>
                                <td id="scientific_name"></td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td id="class"></td>
                            </tr>
                            <tr>
                                <th>Ordo</th>
                                <td id="order"></td>
                            </tr>
                            <tr>
                                <th>Famili</th>
                                <td id="family"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pengamat</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Nama</th>
                                <td id="observer_name"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="observer_email"></td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td id="created_at"></td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td id="updated_at"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3" id="media_preview">
                    <!-- Media preview will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Taxa Detail Modal -->
<div class="modal fade" id="taxaDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Taxa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Taksonomi</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Nama Ilmiah</th>
                                <td id="taxa_scientific_name"></td>
                            </tr>
                            <tr>
                                <th>Famili</th>
                                <td id="taxa_family"></td>
                            </tr>
                            <tr>
                                <th>Genus</th>
                                <td id="taxa_genus"></td>
                            </tr>
                            <tr>
                                <th>Spesies</th>
                                <td id="taxa_species"></td>
                            </tr>
                            <tr>
                                <th>Nama Umum</th>
                                <td id="taxa_common_name"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Lainnya</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Status</th>
                                <td id="taxa_status"></td>
                            </tr>
                            <tr>
                                <th>Diperbarui Oleh</th>
                                <td id="taxa_updated_by"></td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td id="taxa_created_at"></td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td id="taxa_updated_at"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Deskripsi</h6>
                    <p id="taxa_description" class="text-muted"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- FobiUser Detail Modal -->
<div class="modal fade" id="fobiUserDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="user_profile_picture" src="" alt="Profile Picture"
                             class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm">
                            <tr>
                                <th>Nama Lengkap</th>
                                <td id="user_name"></td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td id="user_username"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="user_email"></td>
                            </tr>
                            <tr>
                                <th>Organisasi</th>
                                <td id="user_organization"></td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td id="user_level"></td>
                            </tr>
                            <tr>
                                <th>Bergabung Sejak</th>
                                <td id="user_created_at"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.gridlayer.googlemutant@latest/dist/Leaflet.GoogleMutant.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pre-initialize modal to ensure it's ready
    const activityModal = new bootstrap.Modal(document.getElementById('activityDetailModal'));

    // Checklist Activity Chart
    const checklistCtx = document.getElementById('checklistChart').getContext('2d');
    new Chart(checklistCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($checklistChartData, 'date')) !!},
            datasets: [
                {
                    label: 'FOBI',
                    data: {!! json_encode(array_column($checklistChartData, 'fobi_count')) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Burungnesia',
                    data: {!! json_encode(array_column($checklistChartData, 'burungnesia_count')) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Kupunesia',
                    data: {!! json_encode(array_column($checklistChartData, 'kupunesia_count')) !!},
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.1,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Distribution Chart
    const taxaDistCtx = document.getElementById('taxaDistributionChart').getContext('2d');
    new Chart(taxaDistCtx, {
        type: 'doughnut',
        data: {
            labels: ['FOBI', 'Burungnesia', 'Kupunesia'],
            datasets: [{
                data: [
                    {{ $totalFobiChecklists }},
                    {{ $totalBurungnesiaChecklists }},
                    {{ $totalKupunesiaChecklists }}
                ],
                backgroundColor: [
                    '#0d6efd',  // FOBI - Blue
                    '#198754',  // Burungnesia - Green
                    '#ffc107'   // Kupunesia - Yellow
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Inisialisasi peta
    const map = L.map('taxaMap').setView([-2.5489, 118.0149], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Inisialisasi marker cluster
    const markers = L.markerClusterGroup();

    // Grid size berdasarkan zoom level
    function getGridSize(zoom) {
        if (zoom <= 6) return 1;
        if (zoom <= 8) return 0.5;
        if (zoom <= 10) return 0.2;
        if (zoom <= 12) return 0.1;
        return 0.05;
    }

    // Fungsi untuk membuat grid cells
    function createGridCells(data, gridSize) {
        const grid = {};
        data.forEach(point => {
            const lat = Math.floor(point.latitude / gridSize) * gridSize;
            const lng = Math.floor(point.longitude / gridSize) * gridSize;
            const key = `${lat},${lng}`;

            if (!grid[key]) {
                grid[key] = {
                    count: 0,
                    lat: lat,
                    lng: lng,
                    observations: [] // Store observations instead of points
                };
            }
            grid[key].count++;
            grid[key].observations.push({
                source: point.source,
                scientific_name: point.scientific_name,
                count: point.count
            });
        });
        return grid;
    }

    // Layer untuk grid
    let gridLayer = L.layerGroup();

    // Data taxa locations
    const taxaLocations = {!! json_encode($taxaLocations) !!};

    // Fungsi untuk update grid
    function updateGrid() {
        const zoom = map.getZoom();
        const gridSize = getGridSize(zoom);

        gridLayer.clearLayers();

        if (zoom < 13) {
            const grid = createGridCells(taxaLocations, gridSize);

            Object.values(grid).forEach(cell => {
                const intensity = Math.min(cell.count / 10, 1);
                const color = `rgba(255, 0, 0, ${intensity * 0.7})`;

                const square = L.rectangle(
                    [[cell.lat, cell.lng], [cell.lat + gridSize, cell.lng + gridSize]],
                    {
                        color: 'none',
                        fillColor: color,
                        fillOpacity: 1
                    }
                ).addTo(gridLayer);

                // Create a formatted popup content
                const popupContent = `
                    <div class="grid-popup">
                        <h6 class="mb-2">${cell.count} Observasi</h6>
                        <div class="observation-list" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm">
                                <tbody>
                                    ${cell.observations.map(obs => `
                                        <tr>
                                            <td>
                                                <span class="badge bg-${getSourceColor(obs.source)}">${obs.source}</span>
                                            </td>
                                            <td>${obs.scientific_name}</td>
                                            <td class="text-end">${obs.count}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                square.bindPopup(popupContent, {
                    maxWidth: 300,
                    maxHeight: 300
                });
            });

            map.addLayer(gridLayer);
            map.removeLayer(markers);
        } else {
            markers.clearLayers();
            taxaLocations.forEach(location => {
    if (location.latitude && location.longitude) {
        const markerColor = getSourceColor(location.source);
        const marker = L.marker([location.latitude, location.longitude], {
            icon: L.divIcon({
                className: 'custom-marker',
                html: `<div class="marker-pin bg-${getSourceColor(location.source)}"></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            })
        }).bindPopup(`
            <span class="badge bg-${getSourceColor(location.source)}">${location.source}</span><br>
            <strong>${location.scientific_name}</strong><br>
            ${location.observation_details || 'Tidak ada detail'}<br>
            Jumlah: ${location.count}
        `);
        markers.addLayer(marker);
    }
});

map.addLayer(markers);
            map.removeLayer(gridLayer);
        }
    }

    // Event listener untuk zoom
    map.on('zoomend', updateGrid);

    // Initial update
    updateGrid();

    // Layer controls
    const baseLayers = {
        "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
        "Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}')
    };

    const overlays = {
        "Grid/Markers": gridLayer
    };

    L.control.layers(baseLayers, overlays).addTo(map);
    L.control.scale().addTo(map);

    // Handle klik pada tombol detail
    $('.view-details').on('click', function(e) {
        e.preventDefault();
        const detailUrl = $(this).data('detail-url');
        const modal = $('#activityDetailModal');
        const modalBody = modal.find('.modal-body');

        // Reset dan tampilkan loading
        modalBody.html(`
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        // Ambil detail via AJAX
        fetch(detailUrl)
            .then(response => response.text())
            .then(html => {
                modalBody.html(html);
            })
            .catch(error => {
                modalBody.html(`
                    <div class="alert alert-danger">
                        Terjadi kesalahan saat memuat detail. Silakan coba lagi.
                    </div>
                `);
            });
    });

    // Tambahkan legenda ke peta
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'info legend');
        div.innerHTML = `
            <div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.2);">
                <h6 class="mb-2">Sumber Data</h6>
                <div class="mb-1">
                    <span class="badge bg-primary">FOBI</span>
                </div>
                <div class="mb-1">
                    <span class="badge bg-success">Burungnesia</span>
                </div>
                <div>
                    <span class="badge bg-warning">Kupunesia</span>
                </div>
            </div>
        `;
        return div;
    };
    legend.addTo(map);
});

function showChecklistDetail(id) {
    fetch(`/admin/checklist/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('scientific_name').textContent = data.scientific_name;
            document.getElementById('class').textContent = data.class || '-';
            document.getElementById('order').textContent = data.order || '-';
            document.getElementById('family').textContent = data.family || '-';
            document.getElementById('observer_name').textContent = data.user.name;
            document.getElementById('observer_email').textContent = data.user.email || '-';
            document.getElementById('created_at').textContent = data.created_at;
            document.getElementById('updated_at').textContent = data.updated_at;

            const mediaPreview = document.getElementById('media_preview');
            if (data.media) {
                mediaPreview.innerHTML = `<img src="${data.media}" class="img-fluid" alt="Media">`;
            } else {
                mediaPreview.innerHTML = '<p class="text-muted">Tidak ada media</p>';
            }

            const modal = new bootstrap.Modal(document.getElementById('checklistDetailModal'));
            modal.show();
        });
}

function showTaxaDetail(id) {
    fetch(`/admin/taxa/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('taxa_scientific_name').textContent = data.scientific_name;
            document.getElementById('taxa_family').textContent = data.family || '-';
            document.getElementById('taxa_genus').textContent = data.genus || '-';
            document.getElementById('taxa_species').textContent = data.species || '-';
            document.getElementById('taxa_common_name').textContent = data.common_name || '-';
            document.getElementById('taxa_status').textContent = data.status;
            document.getElementById('taxa_updated_by').textContent = data.updated_by.name;
            document.getElementById('taxa_created_at').textContent = data.created_at;
            document.getElementById('taxa_updated_at').textContent = data.updated_at;
            document.getElementById('taxa_description').textContent = data.description || 'Tidak ada deskripsi';

            const modal = new bootstrap.Modal(document.getElementById('taxaDetailModal'));
            modal.show();
        });
}

function showFobiUserDetail(id) {
    fetch(`/admin/fobiuser/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('user_name').textContent = data.name;
            document.getElementById('user_username').textContent = data.username;
            document.getElementById('user_email').textContent = data.email;
            document.getElementById('user_organization').textContent = data.organization || '-';
            document.getElementById('user_level').textContent = data.level;
            document.getElementById('user_created_at').textContent = data.created_at;

            const profilePic = document.getElementById('user_profile_picture');
            profilePic.src = data.profile_picture
                ? `/storage/${data.profile_picture}`
                : '/images/default-avatar.png';

            const modal = new bootstrap.Modal(document.getElementById('fobiUserDetailModal'));
            modal.show();
        });
}

function loadActivityDetail(type, id, url) {
    const modalContent = document.getElementById('modalContent');

    // Tampilkan loading state
    modalContent.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p class="mt-2">Memuat data...</p></div>';

    // Show modal first
    const modal = new bootstrap.Modal(document.getElementById('activityDetailModal'));
    modal.show();

    fetch(url)
        .then(response => response.json())
        .then(response => {
            if (response.status === 'success') {
                let content = '';
                const data = response.data;

                switch(type) {
                    case 'taxa':
                        content = `
                            <div class="info-group mb-4">
                                <h6 class="text-primary mb-3">Informasi Taksonomi</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">Nama Ilmiah</th>
                                        <td>${data.scientific_name || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Famili</th>
                                        <td>${data.family || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Genus</th>
                                        <td>${data.genus || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Spesies</th>
                                        <td>${data.species || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Umum</th>
                                        <td>${data.common_name || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>${data.status || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Diperbarui Oleh</th>
                                        <td>${data.updated_by?.name || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Update</th>
                                        <td>${data.updated_at || '-'}</td>
                                    </tr>
                                </table>
                            </div>
                            ${data.description ? `
                                <div class="info-group">
                                    <h6 class="text-primary mb-3">Deskripsi</h6>
                                    <p>${data.description}</p>
                                </div>
                            ` : ''}
                        `;
                        break;

                    case 'checklist':
                        content = `
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group mb-4">
                                        <h6 class="text-primary mb-3">Informasi Taksonomi</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Nama Ilmiah</th>
                                                <td>${data.scientific_name || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Kelas</th>
                                                <td>${data.class || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Ordo</th>
                                                <td>${data.order || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Famili</th>
                                                <td>${data.family || '-'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group mb-4">
                                        <h6 class="text-primary mb-3">Detail Observasi</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Observer</th>
                                                <td>${data.user?.name || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal</th>
                                                <td>${data.created_at || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>${data.status || '-'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;
                        break;

                    case 'overseer':
                        content = `
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <img src="${data.profile_picture}"
                                         alt="Profile Picture"
                                         class="img-thumbnail rounded-circle profile-picture"
                                         onerror="this.src='/images/default-avatar.png'"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <h5 class="mt-3 mb-1">${data.name}</h5>
                                    <span class="badge bg-primary">${data.level}</span>
                                </div>
                                <div class="col-md-8">
                                    <div class="info-group">
                                        <h6 class="text-primary mb-3">Informasi Pengguna</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="30%">Username</th>
                                                <td>${data.username || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>${data.email || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Organisasi</th>
                                                <td>${data.organization || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Bergabung Sejak</th>
                                                <td>${data.created_at || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Login Terakhir</th>
                                                <td>${data.last_login || '-'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    ${data.bio || data.expertise ? `
                                        <div class="info-group mt-4">
                                            <h6 class="text-primary mb-3">Bio & Keahlian</h6>
                                            ${data.bio ? `
                                                <div class="mb-3">
                                                    <h6 class="text-muted mb-2">Biografi</h6>
                                                    <p>${data.bio}</p>
                                                </div>
                                            ` : ''}
                                            ${data.expertise ? `
                                                <div>
                                                    <h6 class="text-muted mb-2">Bidang Keahlian</h6>
                                                    <p>${data.expertise}</p>
                                                </div>
                                            ` : ''}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                        break;
                }

                modalContent.innerHTML = content;
            } else {
                throw new Error(response.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Terjadi kesalahan saat memuat data: ${error.message}
                </div>
            `;
        });
}

// Fungsi helper untuk menentukan warna berdasarkan sumber
function getSourceColor(source) {
    switch(source) {
        case 'fobi':
            return 'primary';
        case 'burungnesia':
            return 'success';
        case 'kupunesia':
            return 'warning';
        default:
            return 'secondary';
    }
}

// Tambahkan CSS untuk marker kustom
const styles = `
    <style>
        .custom-marker {
            width: 30px;
            height: 30px;
        }
        .marker-pin {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 4px rgba(0,0,0,0.3);
        }
        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; }
    </style>
`;
document.head.insertAdjacentHTML('beforeend', styles);
</script>
@endsection

@section('styles')
<style>
    .grid-popup {
    padding: 5px;
}

.grid-popup .table {
    margin-bottom: 0;
}

.grid-popup .table td {
    padding: 4px 8px;
    vertical-align: middle;
}

.grid-popup .badge {
    font-size: 0.75rem;
}

.observation-list::-webkit-scrollbar {
    width: 6px;
}

.observation-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.observation-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.observation-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    height: 100%;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 1.5rem;
}

.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-content {
    position: relative;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

/* Stats Card Styles */
.stats-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

/* Table Styles */
.table > :not(caption) > * > * {
    padding: 1rem;
}

.dropdown {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.btn-light {
    background: transparent;
    border: none;
    padding: 0.25rem 0.5rem;
}

.btn-light:hover {
    background: rgba(0,0,0,0.05);
}

/* Modal Styles */
.modal-lg {
    max-width: 1000px;
}

/* Info Group Styles */
.info-group {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Table Styles */
.table {
    margin-bottom: 0;
}

.table th {
    color: #495057;
    font-weight: 600;
    padding: 12px 8px;
}

.table td {
    color: #212529;
    padding: 12px 8px;
}

/* Media Section Styles */
.media-section img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Profile Picture Styles */
.profile-picture {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Text Styles */
.text-primary {
    color: #0d6efd !important;
}

.text-muted {
    color: #6c757d !important;
}
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.gridlayer.googlemutant@latest/dist/Leaflet.GoogleMutant.css" />
@endsection
