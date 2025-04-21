<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Lokasi Genus: {{ $genus }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>Peta Lokasi Genus: {{ $genus }}</h1>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([-0.789275, 113.921327], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var locations = @json($locations);

        var gridSizes = [0.4, 0.2, 0.05, 0.02]; // Ukuran grid dari besar ke kecil
        var grids = [{}, {}, {}, {}]; // Array untuk menyimpan grid di setiap level
        var markers = [];
        locations.forEach(function(location) {
            gridSizes.forEach(function(gridSize, index) {
                let lat = Math.floor(location.latitude / gridSize) * gridSize;
                let lng = Math.floor(location.longitude / gridSize) * gridSize;
                let key = lat + ',' + lng;

                if (!grids[index][key]) {
                    grids[index][key] = 0;
                }
                grids[index][key]++;
            });
        });

        function getColor(count) {
            return count > 5 ? 'red' :
                   count > 2 ? 'orange' :
                   'orange';
        }

        var rectangles = [[], [], [], []]; // Array untuk menyimpan persegi panjang di setiap level

        function addGrid(level) {
            for (var key in grids[level]) {
                var parts = key.split(',');
                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);
                var count = grids[level][key];

                var bounds = [
                    [lat, lng],
                    [lat + gridSizes[level], lng + gridSizes[level]]
                ];

                var rectangle = L.rectangle(bounds, {
                    color: getColor(count),
                    fillColor: getColor(count),
                    fillOpacity: 0.5,
                    weight: 1
                }).addTo(map).bindPopup(`Count: ${count}`);

                rectangles[level].push(rectangle);
            }
        }

        function clearGrids() {
            rectangles.forEach(function(rects) {
                rects.forEach(function(rect) {
                    map.removeLayer(rect);
                });
            });
        }

        function addMarkers() {
            locations.forEach(function(location) {
                var bounds = [
                    [location.latitude - 0.004, location.longitude - 0.004],
                    [location.latitude + 0.004, location.longitude + 0.004]
                ];
                var rectangle = L.rectangle(bounds, {
                    color: 'crimson',
                    fillColor: 'crimson',
                    fillOpacity: 0.5,
                    weight: 1
                }).addTo(map);
                markers.push(rectangle);
            });
        }

        function clearMarkers() {
            markers.forEach(function(marker) {
                map.removeLayer(marker);
            });
            markers = [];
        }

        map.on('zoomend', function() {
            var zoomLevel = map.getZoom();
            clearGrids();
            clearMarkers();
            if (zoomLevel > 11) {
                addMarkers();
            } else if (zoomLevel >= 10) {
                addGrid(3);
            } else if (zoomLevel >= 8) {
                addGrid(2);
            } else if (zoomLevel >= 6) {
                addGrid(1);
            } else {
                addGrid(0);
            }
        });

        // Inisialisasi grid terbesar
        addGrid(0);
    </script>
</body>
</html>
