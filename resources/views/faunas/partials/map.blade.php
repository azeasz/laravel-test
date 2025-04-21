<div class="map" id="map" style="height: 400px;">
    <button id="clearSearchBtn" class="btn btn-dark" style="position: absolute; top: 50px; right: 10px; z-index: 1000; background-color: rgba(0, 0, 0, 0.2);"><i class="fa fa-refresh"></i></button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map', {
                scrollWheelZoom: false // Disable scroll wheel zoom
            }).setView([-0.789275, 113.921327], 4.8);

            function updateMapView() {
                if (window.innerWidth <= 768) { // Mobile view
                    map.setView([-0.789275, 113.921327], 3.4); // Set different view for mobile
                } else { // Desktop view
                    map.setView([-0.789275, 113.921327], 4.8); // Default view for desktop
                }
            }

            // Initial call
            updateMapView();

            // Update view on window resize
            window.addEventListener('resize', updateMapView);

            var zoomTooltip = L.tooltip({
                permanent: true,
                direction: 'top',
                className: 'zoom-tooltip',
                offset: [100, 0]
            }).setContent('Tekan Ctrl + Scroll untuk zoom');

            map.on('mousemove', function(e) {
                zoomTooltip.setLatLng(map.getBounds().getSouthWest()).addTo(map);
            });

            map.on('mouseover', function(e) {
                zoomTooltip.setLatLng(e.latlng).addTo(map);
            });

            map.on('mouseout', function() {
                map.removeLayer(zoomTooltip);
            });

            map.on('keydown keyup', function(e) {
                if (e.originalEvent.ctrlKey || e.originalEvent.metaKey) { // Enable for both Ctrl (Windows) and Command (Mac)
                    map.scrollWheelZoom.enable(); // Enable scroll wheel zoom when Ctrl or Command is pressed
                } else {
                    map.scrollWheelZoom.disable(); // Disable scroll wheel zoom when Ctrl or Command is released
                }
            });

            var googleSat = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            var googleHybrid = L.tileLayer('http://{s}.google.com/vt?lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            var googleTerrain = L.tileLayer('http://{s}.google.com/vt?lyrs=p&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            var baseMaps = {
                "Satelit": googleSat,
                "Hybrid": googleHybrid,
                "Terrain": googleTerrain
            };

            L.control.layers(baseMaps, null, { position: 'topleft' }).addTo(map);
            googleSat.addTo(map); // Set default layer

            var customAttribution = L.control.attribution({
                position: 'bottomright'
            });

            customAttribution.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'custom-attribution');
                div.innerHTML = `<img src="{{ asset('storage/icon/FOBi.png') }}" alt="Logo" style="width:17px;height:16px;">`;
                return div;
            };

            customAttribution.addTo(map);

            var smallIcon = L.icon({
                iconUrl: '{{ asset('storage/logo/wth.png') }}',
                iconSize: [12, 9]
            });

            function getColor(count) {
                return count > 50 ? 'rgba(128, 0, 38, 0.5)' :
                    count > 20 ? 'rgba(189, 0, 38, 0.5)' :
                    count > 10 ? 'rgba(227, 26, 28, 0.5)' :
                    count > 5 ? 'rgba(252, 78, 42, 0.5)' :
                    count > 2 ? 'rgba(253, 141, 60, 0.5)' :
                    'rgba(254, 180, 76, 0.5)';
            }

            var gridSize = 0.02;
            var largeGridSize = 0.05; // Ukuran grid besar
            var extraLargeGridSize = 0.2; // Ukuran grid ekstra besar
            var ultraLargeGridSize = 0.5; // Ukuran grid ultra besar
            var grid = {};
            var largeGrid = {}; // Grid besar
            var extraLargeGrid = {}; // Grid ekstra besar
            var ultraLargeGrid = {}; // Grid ultra besar
            var checklists = [];

            @foreach ($faunas as $fauna)
            @foreach ($fauna->checklists as $checklist)
            {
                let lat = Math.floor({{ $checklist->latitude }} / gridSize) * gridSize;
                let lng = Math.floor({{ $checklist->longitude }} / gridSize) * gridSize;
                let key = lat + ',' + lng;

                if (!grid[key]) {
                    grid[key] = 0;
                }
                grid[key]++;

                // Tambahkan ke grid besar
                let largeLat = Math.floor({{ $checklist->latitude }} / largeGridSize) * largeGridSize;
                let largeLng = Math.floor({{ $checklist->longitude }} / largeGridSize) * largeGridSize;
                let largeKey = largeLat + ',' + largeLng;

                if (!largeGrid[largeKey]) {
                    largeGrid[largeKey] = 0;
                }
                largeGrid[largeKey]++;

                // Tambahkan ke grid ekstra besar
                let extraLargeLat = Math.floor({{ $checklist->latitude }} / extraLargeGridSize) * extraLargeGridSize;
                let extraLargeLng = Math.floor({{ $checklist->longitude }} / extraLargeGridSize) * extraLargeGridSize;
                let extraLargeKey = extraLargeLat + ',' + extraLargeLng;

                if (!extraLargeGrid[extraLargeKey]) {
                    extraLargeGrid[extraLargeKey] = 0;
                }
                extraLargeGrid[extraLargeKey]++;

                // Tambahkan ke grid ultra besar
                let ultraLargeLat = Math.floor({{ $checklist->latitude }} / ultraLargeGridSize) * ultraLargeGridSize;
                let ultraLargeLng = Math.floor({{ $checklist->longitude }} / ultraLargeGridSize) * ultraLargeGridSize;
                let ultraLargeKey = ultraLargeLat + ',' + ultraLargeLng;

                if (!ultraLargeGrid[ultraLargeKey]) {
                    ultraLargeGrid[ultraLargeKey] = 0;
                }
                ultraLargeGrid[ultraLargeKey]++;
                checklists.push({
                    lat: {{ $checklist->latitude }},
                    lng: {{ $checklist->longitude }},
                    nameId: "{{ $fauna->nameId }}",
                    nameLat: "{{ $fauna->nameLat }}",
                    user_id: "{{ $checklist->user_id }}",
                    observer: "{{ in_array($fauna->nameId, ['Burung-gereja erasia', 'trinil semak', 'perenjak rawa']) ? '' : $checklist->observer }}",
                    count: "{{ $checklist->pivot->count }}",
                    notes: "{{ $checklist->pivot->notes }}",
                    date: "{{ $checklist->created_at }}",
                    profile_picture: "{{ $checklist->user ? $checklist->user->profile_picture : '' }}" // Tambahkan pengecekan null
                });
            }
            @endforeach
            @endforeach

            var rectangles = {}; // Object to store rectangles with unique IDs
            var largeRectangles = {}; // Object to store large rectangles
            var extraLargeRectangles = {}; // Object to store extra large rectangles
            var ultraLargeRectangles = {}; // Object to store ultra large rectangles
            var markers = []; // Array to store markers
            var boundaryGrid = {}; // Grid khusus untuk boundary

            var filterActive = false; // Variabel untuk memeriksa apakah filter aktif
            var boundaryMarkers = []; // Array untuk menyimpan marker boundary

            function addGrid(opacity = 1, level = 1) {
                var size = level === 2 ? gridSize * 0.99 : (level === 3 ? gridSize / 4 : gridSize);
                for (var key in grid) {
                    var parts = key.split(',');
                    var lat = parseFloat(parts[0]);
                    var lng = parseFloat(parts[1]);
                    var count = grid[key];

                    var bounds = [
                        [lat, lng],
                        [lat + size, lng + size]
                    ];

                    var rectangle = L.rectangle(bounds, {
                        color: getColor(count),
                        opacity: opacity,
                        fillColor: getColor(count),
                        fillOpacity: opacity,
                        weight: 1,
                        className: 'grid-level-' + level,
                        interactive: false // Make the grid non-interactive
                    }).addTo(map).bindPopup(`Count: ${count}<br>Luas: ${(size * 111).toFixed(2)} km²`);

                    // Assign a unique ID to the rectangle
                    var rectId = `rect-${lat}-${lng}-${size}`;
                    rectangles[rectId] = rectangle;

                    // Hide the grid initially
                    rectangle.setStyle({ opacity: 0, fillOpacity: 0 });
                }
            }

            function addLargeGrid(opacity = 1) {
                for (var key in largeGrid) {
                    var parts = key.split(',');
                    var lat = parseFloat(parts[0]);
                    var lng = parseFloat(parts[1]);
                    var count = largeGrid[key];

                    var bounds = [
                        [lat, lng],
                        [lat + largeGridSize, lng + largeGridSize]
                    ];

                    var rectangle = L.rectangle(bounds, {
                        color: getColor(count),
                        opacity: opacity,
                        fillColor: getColor(count),
                        fillOpacity: opacity,
                        weight: 1,
                        className: 'large-grid'
                    }).addTo(map).bindPopup(`Count: ${count}<br>Luas: ${(largeGridSize * 111).toFixed(2)} km²`);

                    // Assign a unique ID to the rectangle
                    var rectId = `large-rect-${lat}-${lng}-${largeGridSize}`;
                    largeRectangles[rectId] = rectangle;

                    rectangle.setStyle({ opacity: 0, fillOpacity: 0 });
                }
            }

            function addExtraLargeGrid(opacity = 1) {
                for (var key in extraLargeGrid) {
                    var parts = key.split(',');
                    var lat = parseFloat(parts[0]);
                    var lng = parseFloat(parts[1]);
                    var count = extraLargeGrid[key];

                    var bounds = [
                        [lat, lng],
                        [lat + extraLargeGridSize, lng + extraLargeGridSize]
                    ];

                    var rectangle = L.rectangle(bounds, {
                        color: getColor(count),
                        opacity: opacity,
                        fillColor: getColor(count),
                        fillOpacity: opacity,
                        weight: 1,
                        className: 'extra-large-grid'
                    }).addTo(map).bindPopup(`Count: ${count}<br>Luas: ${(extraLargeGridSize * 111).toFixed(2)} km²`);

                    // Assign a unique ID to the rectangle
                    var rectId = `extra-large-rect-${lat}-${lng}-${extraLargeGridSize}`;
                    extraLargeRectangles[rectId] = rectangle;

                    rectangle.setStyle({ opacity: 0, fillOpacity: 0 });
                }
            }

            function addUltraLargeGrid(opacity = 1) {
                for (var key in ultraLargeGrid) {
                    var parts = key.split(',');
                    var lat = parseFloat(parts[0]);
                    var lng = parseFloat(parts[1]);
                    var count = ultraLargeGrid[key];

                    var bounds = [
                        [lat, lng],
                        [lat + ultraLargeGridSize, lng + ultraLargeGridSize]
                    ];

                    var rectangle = L.rectangle(bounds, {
                        color: getColor(count),
                        opacity: opacity,
                        fillColor: getColor(count),
                        fillOpacity: opacity,
                        weight: 1,
                        className: 'ultra-large-grid'
                    }).addTo(map).bindPopup(`Count: ${count}<br>Luas: ${(ultraLargeGridSize * 111).toFixed(2)} km²`);

                    // Assign a unique ID to the rectangle
                    var rectId = `ultra-large-rect-${lat}-${lng}-${ultraLargeGridSize}`;
                    ultraLargeRectangles[rectId] = rectangle;
                }
            }

            function addMarkers() {
                checklists.forEach(function(checklist) {
                    var bounds = [
                        [checklist.lat - 0.004, checklist.lng - 0.004],
                        [checklist.lat + 0.004, checklist.lng + 0.004]
                    ];
                    var rectangle = L.rectangle(bounds, {
                        color: getColor(checklist.count),
                        fillColor: getColor(checklist.count),
                        fillOpacity: 0.5,
                        weight: 1
                    }).addTo(map).bindPopup(`NameId: ${checklist.nameId}<br>Observer: ${checklist.observer}<br>Count: ${checklist.count}<br>Notes: ${checklist.notes}<br>Date: ${checklist.date}`);
                    markers.push(rectangle); // Store marker reference
                });
            }

            function clearRectangles() {
                for (var rectId in rectangles) {
                    map.removeLayer(rectangles[rectId]);
                }
            }

            function clearLargeRectangles() {
                for (var rectId in largeRectangles) {
                    map.removeLayer(largeRectangles[rectId]);
                }
            }

            function clearExtraLargeRectangles() {
                for (var rectId in extraLargeRectangles) {
                    map.removeLayer(extraLargeRectangles[rectId]);
                }
            }

            function clearUltraLargeRectangles() {
                for (var rectId in ultraLargeRectangles) {
                    map.removeLayer(ultraLargeRectangles[rectId]);
                }
            }

            function clearMarkers() {
                markers.forEach(function(marker) {
                    map.removeLayer(marker);
                });
                markers = []; // Clear the markers array
            }

            function clearBoundaryGrid() {
                for (var rectId in boundaryGrid) {
                    map.removeLayer(boundaryGrid[rectId]);
                }
                boundaryGrid = {}; // Clear the boundary grid array
            }

            addGrid();
            addLargeGrid(); // Tambahkan grid besar
            addExtraLargeGrid(); // Tambahkan grid ekstra besar
            addUltraLargeGrid(); // Tambahkan grid ultra besar

            map.on('zoomend', function() {
                var zoomLevel = map.getZoom();
                clearRectangles();
                clearLargeRectangles();
                clearExtraLargeRectangles();
                clearUltraLargeRectangles();
                clearMarkers();

                if (!filterActive) { // Periksa apakah filter tidak aktif
                    if (zoomLevel > 11) {
                        addMarkers();
                    } else if (zoomLevel >= 10 && zoomLevel <= 11) {
                        addGrid(1, 2);
                    } else if (zoomLevel >= 8 && zoomLevel <= 10) {
                        addLargeGrid();
                    } else if (zoomLevel >= 6 && zoomLevel <= 8) {
                        addExtraLargeGrid();
                    } else {
                        addUltraLargeGrid();
                    }
                } else { // Jika filter aktif
                    if (zoomLevel > 9) {
                        addBoundaryMarkers();
                    } else {
                        clearBoundaryMarkers();
                        filterGridByBoundary(boundaryLayer);
                    }
                }
            });

            // Add place search
            var searchControl = L.esri.Geocoding.geosearch().addTo(map);

            var results = L.layerGroup().addTo(map);

            var boundaryLayer;

            searchControl.on('results', function(data) {
                results.clearLayers();
                if (boundaryLayer) {
                    map.removeLayer(boundaryLayer);
                }
                for (var i = data.results.length - 1; i >= 0; i--) {
                    var result = data.results[i];

                    // Tampilkan loading indicator
                    $('#loadingIndicator').show();

                    // Ganti marker dengan boundary area dari GeoJSON
                    fetch('{{ asset('storage/ip.geojson') }}')
                        .then(response => response.json())
                        .then(geojsonData => {
                            if (boundaryLayer) {
                                map.removeLayer(boundaryLayer);
                            }

                            var filteredData = {
                                "type": "FeatureCollection",
                                "features": geojsonData.features.filter(function(feature) {
                                    if (feature.geometry && feature.geometry.coordinates) {
                                        var bounds = L.geoJSON(feature).getBounds();
                                        return bounds.contains(result.latlng);
                                    }
                                    return false;
                                })
                            };

                            // Set filterActive to true
                            filterActive = true;

                            boundaryLayer = L.geoJSON(filteredData, {
                                style: function(feature) {
                                    return {
                                        color: 'purple',
                                        weight: 3,
                                        fillOpacity: 0.1,
                                        opacity: 0.5
                                    };
                                },
                            }).addTo(map);

                            // Adjust map view to fit the boundary
                            map.fitBounds(boundaryLayer.getBounds());

                            // Filter grid berdasarkan boundary
                            filterGridByBoundary(boundaryLayer);

                            // Sembunyikan loading indicator
                            $('#loadingIndicator').hide();
                        })
                        .catch(error => {
                            console.error('Error loading GeoJSON:', error);
                            // Sembunyikan loading indicator jika terjadi error
                            $('#loadingIndicator').hide();
                        });
                }
            });

            var filteredFaunas = []; // Array untuk menyimpan fauna yang difilter
            function filterGridByBoundary(boundaryLayer) {
                var boundaryBounds = boundaryLayer.getBounds();
                filteredFaunas = [];
                clearBoundaryGrid(); // Clear previous boundary grid
                clearUltraLargeRectangles();
                clearExtraLargeRectangles();
                clearLargeRectangles();
                clearRectangles();
                var zoomLevel = map.getZoom();

function addFilteredGrid(rectangles, gridSize) {
    for (var rectId in rectangles) {
        var rect = rectangles[rectId];
        if (boundaryBounds.intersects(rect.getBounds())) {
            var newRect = L.rectangle(rect.getBounds(), {
                color: rect.options.color,
                fillColor: rect.options.fillColor,
                fillOpacity: rect.options.fillOpacity,
                weight: rect.options.weight,
                className: rect.options.className,
                interactive: rect.options.interactive
            }).addTo(map);
            boundaryGrid[rectId] = newRect;

            // Tambahkan fauna yang berada di dalam boundary ke array filteredFaunas
            checklists.forEach(function(checklist) {
                if (rect.getBounds().contains([checklist.lat, checklist.lng])) {
                    filteredFaunas.push(checklist);
                }
            });
        } else {
            rect.setStyle({ opacity: 0, fillOpacity: 0 });
        }
    }
}

if (zoomLevel > 8) {
    addFilteredGrid(rectangles, gridSize);
} else {
    addFilteredGrid(largeRectangles, largeGridSize);
}

filterActive = true; // Set filterActive to true

// Tampilkan daftar fauna yang difilter dalam bentuk popup
displayFilteredFaunasPopup(boundaryLayer);
}

function displayFilteredFaunasPopup(boundaryLayer) {
var popupContent = '<div style="max-height: 200px; overflow-y: auto;"><ul>';
var uniqueFaunas = [];

filteredFaunas.forEach(function(fauna) {
    if (!uniqueFaunas.some(f => f.nameId === fauna.nameId && f.date === fauna.date)) {
        uniqueFaunas.push(fauna);
        var profilePictureUrl = fauna.profile_picture ? `/storage/uploads/profile_pictures/${fauna.profile_picture}` : '/storage/logo/user.png'; // Path ke gambar profil
        popupContent += `
            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                <img src="${profilePictureUrl}" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                <div>
                    <strong>${fauna.nameId}</strong><br>
                    <em>${fauna.nameLat}</em><br>
                    <span>${fauna.observer}</span><br>
                    <span>${fauna.date}</span>
                </div>
            </li>
        `;
    }
});

popupContent += '</ul><button onclick="closePopup()">Tutup</button></div>';
boundaryLayer.bindPopup(popupContent).openPopup();
}

function closePopup() {
map.closePopup();
}

function addBoundaryMarkers(boundaryLayer) {
var addedChecklists = new Set(); // Set untuk melacak checklist yang sudah ditambahkan

for (var rectId in boundaryGrid) {
    var rect = boundaryGrid[rectId];
    var bounds = rect.getBounds();

    // Cari checklist yang sesuai dengan bounds
    var matchingChecklist = checklists.find(function(checklist) {
        return bounds.contains([checklist.lat, checklist.lng]) && !addedChecklists.has(checklist);
    });

    if (matchingChecklist) {
        var smallBounds = [
            [matchingChecklist.lat - 0.004, matchingChecklist.lng - 0.004],
            [matchingChecklist.lat + 0.004, matchingChecklist.lng + 0.004]
        ];

        var rectangle = L.rectangle(smallBounds, {
            color: getColor(matchingChecklist.count),
            fillColor: getColor(matchingChecklist.count),
            fillOpacity: 0.5,
            weight: 1
        }).addTo(map).bindPopup(`
            NameId: ${matchingChecklist.nameId}<br>
            Observer: ${matchingChecklist.observer}<br>
            Count: ${matchingChecklist.count}<br>
            Notes: ${matchingChecklist.notes}<br>
            Date: ${matchingChecklist.date}
        `);
        boundaryMarkers.push(rectangle);
        addedChecklists.add(matchingChecklist); // Tambahkan checklist ke Set
    }

    map.removeLayer(rect);
}
}

function clearBoundaryMarkers() {
boundaryMarkers.forEach(function(marker) {
    map.removeLayer(marker);
});
boundaryMarkers = [];
}

map.on('zoomend', function() {
var zoomLevel = map.getZoom();
var opacity = zoomLevel > 20 ? 0.9 : 1;
for (var rectId in rectangles) {
    rectangles[rectId].setStyle({ fillOpacity: opacity, opacity: opacity });
}
for (var rectId in largeRectangles) {
    largeRectangles[rectId].setStyle({ fillOpacity: opacity, opacity: opacity });
}
for (var rectId in extraLargeRectangles) {
    extraLargeRectangles[rectId].setStyle({ fillOpacity: opacity, opacity: opacity });
}
for (var rectId in ultraLargeRectangles) {
    ultraLargeRectangles[rectId].setStyle({ fillOpacity: opacity, opacity: opacity });
}
for (var rectId in boundaryGrid) {
    boundaryGrid[rectId].setStyle({ fillOpacity: opacity, opacity: opacity });
}
});

// Function to reset the map to its initial state
function resetMap() {
if (boundaryLayer) {
    map.removeLayer(boundaryLayer);
}
filteredFaunas = [];
results.clearLayers();
map.setView([-0.789275, 113.921327], 4.8);
for (var rectId in rectangles) {
    rectangles[rectId].setStyle({ opacity: 1, fillOpacity: 1 });
}
for (var rectId in largeRectangles) {
    largeRectangles[rectId].setStyle({ opacity: 1, fillOpacity: 1 });
}
for (var rectId in extraLargeRectangles) {
    extraLargeRectangles[rectId].setStyle({ opacity: 1, fillOpacity: 1 });
}
for (var rectId in ultraLargeRectangles) {
    ultraLargeRectangles[rectId].setStyle({ opacity: 1, fillOpacity: 1 });
}
clearBoundaryGrid(); // Clear boundary grid
clearBoundaryMarkers(); // Clear boundary markers

filterActive = false; // Reset filterActive to false
}

// Add event listener to the "Clear Search" button
document.getElementById('clearSearchBtn').addEventListener('click', resetMap);
});
</script>
</div>
