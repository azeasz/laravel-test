<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Fobi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> --}}
    <link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@2.3.3/dist/esri-leaflet-geocoder.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/esri-leaflet@2.3.3/dist/esri-leaflet.js"></script>
    <script src="https://unpkg.com/esri-leaflet-geocoder@2.3.3/dist/esri-leaflet-geocoder.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        header {
            background-color: #f0f0f0;
            height: 90px;
            width: 100%;
            z-index: 5000;
            transition: top 0.3s; /* Tambahkan transisi untuk efek halus */
        }

        .fixed-header {
            position: fixed;
            top: 0;
            border-bottom: 5px solid #679995;
        }

        /* Tambahkan padding-top pada body untuk menghindari konten tertutup header */
        body.fixed-header-padding {
            padding-top: 90px; /* Sesuaikan dengan tinggi header */
        }
        .logo img {
            height: 65px;
            padding-bottom: 5px;
        }

        .user-info {
            display: flex;
            gap: 20px;
            position: relative;
            padding-left: 20px;
            padding-top: 30px;
            border-left: 1px solid black;
            bottom: 10px;
        }

        .user-info i{
            position: relative;
            top: 5px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0px 30px;
            color: rgb(0, 0, 0);
            height: 8vh;
            font-size: 14px;
        }
        nav .links {
            display: flex;
        }
        .links p, a {
            margin-top: 50px;
            padding: 10px;
            color: rgb(0, 0, 0);
            text-decoration: none;
        }

        #mobile-menu-btn {
            display: none;
        }
        .mobile-menu {
            color: rgb(0, 0, 0);
            background-color: rgb(255, 255, 255);
            display: none;
            position: absolute;
            right: 0;
            top: 0;
            height: 100vh;
            width: 50vw;
            z-index: 10000;
            border-left: 4px solid #679995;
            border-bottom: 4px solid #679995;
        }

        .mobile-menu a{
            color: rgb(0, 0, 0);
            text-decoration: none;
            padding: 0;
            margin: 0;
            position: relative;
            top: 0;
            font-size: 12px;
            bottom: 20px;
            border-bottom: 1px solid #679995;
        }


        @media (max-width: 988px) {
            body{
                overflow-x: hidden;
            }
            #mobile-menu-btn {
                display: block;
                cursor: pointer;
                right: 100px;
            }
            nav p {
                display: none;
            }
            .mobile-menu {
                display: none;
                flex-direction: column;
                justify-content: space-around;
                align-items: center;
            }
        }

        .dropdown-menu {
            margin: 0;
            padding: 0;
            gap: 0;
        }

        .stats {
            background-color: #679995; /* Warna hijau */
            color: white; /* Warna teks putih */
            position: relative;
            height: 80px;
            margin: 0;
        }

        .filter {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
        }

        .filter input {
            padding: 10px;
            border: 1px solid #ccc;
            color: white;
        }

        .filter button {
            padding: 5px 10px;
            color: white;
            border: none;
            cursor: pointer;
            width: 25%;
            height: 25%;
            margin-right: 10px;
        }

        .filter ::placeholder {
            color: rgb(244, 244, 244);
            font-size: 12px;
        }

        .view-icons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .view-icons i {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .view-icons :active{
            background-color: #555;
        }

        .view-icons :hover{
            background-color: #8d8d8d;
        }

        .tree-view ul {
            list-style-type: none;
        }

        .tree-view li {
            cursor: pointer;
            margin: 5px 0;
        }

        .tree-view .nested {
            display: none;
        }

        .tree-view .active {
            display: block;
        }

        .content {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .content.list-view {
            flex-direction: column;
        }

        .card {
            width: 13rem;
            overflow: hidden;
        }

        .card.list-view {
            width: 100%;
            display: flex;
            flex-direction: row;
            border-radius: 0;
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card.list-view img {
            width: 180px;
            height: auto;
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 0.775rem;
            color: #555;
        }

        .card-footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            #mobile-menu-btn {
                display: block;
                cursor: pointer;
                right: 100px;
            }
            nav p {
                display: none;
            }
            .mobile-menu {
                display: none;
                flex-direction: column;
                justify-content: space-around;
                align-items: center;
            }
            .stats {
                font-size: 0.8rem; /* Adjust font size for smaller screens */
                padding: 10px; /* Adjust padding for smaller screens */
                height: 200px;
            }
            .filter {
                gap: 5px;
            }
            .filter input {
                width: 100%; /* Full width for inputs and buttons */
                margin-right: 0;
            }
            .filter button {
                width: 30%;
            }
            .view-icons {
                justify-content: center;
                width: 70%;
            }
            .content {
                flex-direction: column;
                gap: 10px;
            }
            .card {
                width: 100%;
            }
            .card img {
                height: auto;
            }
            .list-view-table thead {
                display: none;
            }
        }

        @media (max-width: 425px) {
            .user-info{
                gap: 5px;
                position: relative;
                right: 10px;
            }
            .user-info span {
                position: relative;
                right: 5px;
            }

            #mobile-menu-btn{
                right: 5px;
            }

            .filter {
                font-size: 10px;
            }

            .view-icons {
                font-size: 10px;
            }
        }

        /* Tambahan CSS untuk tampilan list view */
        .list-view-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .list-view-table tr{
            background-color: #fffdfd;
            border-bottom: 2px solid #f0f0f0;
        }

        .list-view-table th, .list-view-table td {
            border: none;
            padding: 8px;
        }

        .list-view-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .list-view-table img {
            width: 100px;
            height: auto;
        }

        .label img{
            width: 50px;
            height: auto;
        }

        #map {
            height: 500px; /* Atur tinggi peta sesuai kebutuhan */
            width: 100%;
            display: none; /* Sembunyikan peta secara default */
        }

        .leaflet-control-attribution {
            display: none;
        }

     .leaflet-control-attribution,
        .esri-truncated-attribution {
            display: none !important;
        }

         .leaflet-control-zoom-in span {
          display: flex;
          font-size: 15px;
          position: relative;
          bottom: 10px;
       }

        .leaflet-control-zoom-out span {
          display: flex;
          font-size: 15px;
          position: relative;
          bottom: 10px;
       }

       .leaflet-control-zoom a {
           width: 30px; /* Atur lebar tombol zoom */
           height: 30px; /* Atur tinggi tombol zoom */
           line-height: 30px; /* Atur tinggi garis tombol zoom */
           font-size: 18px; /* Atur ukuran font tombol zoom */
           position: relative;
           display: flex;
           margin: 0;
       }

       .leaflet-control-layers-toggle {
           margin: 0;
           position: relative;
           bottom: 0;
           display: flex;
           font-size: 15px;
           height: 50px;
       }

       .leaflet-popup-close-button {
           position: relative;
           margin: 0;
           bottom: 5px;
       }

        /* Responsif untuk mobile */
        @media (max-width: 600px) {
            .list-view-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .list-view-table th, .list-view-table td {
                display: block;
                text-align: right;
            }

            .list-view-table th {
                text-align: left;
            }

            .list-view-table td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
            }

            .list-view-table td:last-child {
                border-bottom: 0;
            }
        }

        /* CSS untuk tampilan pohon */
        .tree-view {
            --spacing: 1.5rem;
            --radius: 10px;
            margin-top: 20px;
        }
        .tree-view li {
            display: block;
            position: relative;
            padding-left: calc(2 * var(--spacing) - var(--radius) - 2px);
        }
        .tree-view ul {
            margin-left: calc(var(--radius) - var(--spacing));
            padding-left: 0;
        }
        .tree-view ul li {
            border-left: 2px solid #ddd;
        }
        .tree-view ul li:last-child {
            border-color: transparent;
        }
        .tree-view ul li::before {
            content: '';
            display: block;
            position: absolute;
            top: calc(var(--spacing) / -2);
            left: -2px;
            width: calc(var(--spacing) + 2px);
            height: calc(var(--spacing) + 1px);
            border: solid #ddd;
            border-width: 0 0 2px 2px;
        }
        .tree-view summary {
            display: block;
            cursor: pointer;
        }
        .tree-view summary::marker,
        .tree-view summary::-webkit-details-marker {
            display: none;
        }
        .tree-view summary:focus {
            outline: none;
        }
        .tree-view summary:focus-visible {
            outline: 1px dotted #000;
        }
        .tree-view li::after,
        .tree-view summary::before {
            content: '';
            display: block;
            position: absolute;
            top: calc(var(--spacing) / 2 - var(--radius));
            left: calc(var(--spacing) - var(--radius) - 1px);
            width: calc(2 * var(--radius));
            height: calc(2 * var(--radius));
            border-radius: 0;
            background: #ddd;
        }
        .tree-view summary::before {
            z-index: 1;
        }
        .tree-view details[open] > summary::before {
            background-color: #679995;
        }


        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }

        .sidebar {
        position: absolute;
        right: 0;
        top: 0;
        width: 300px;
        height: 100%;
        background-color: #679995; /* Warna hijau */
        color: white;
        display: none; /* Sembunyikan sidebar secara default */
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar-content {
        padding: 20px;
    }

    .sidebar-content h3 {
        margin-top: 0;
    }

    .sidebar-content p {
        margin: 5px 0;
    }

    .close-sidebar {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
    }

    .list-view-only {
        display: none;
    }
    .list-view .list-view-only {
        display: block;
    }
    </style>
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const gridViewIcon = document.querySelector('.fa-th-large');
        const listViewIcon = document.querySelector('.fa-th-list');
        const treeViewIcon = document.querySelector('.tree-icon');
        const mapViewIcon = document.querySelector('.fa-map');
        const content = document.querySelector('.content');
        const treeView = document.querySelector('.tree-view');
        const mapContainer = document.getElementById('map');
        const sidebar = document.getElementById('sidebar');
        const sidebarContent = document.getElementById('sidebar-content');
        const closeSidebarButton = document.getElementById('close-sidebar');
        let map; // Declare map variable here

        gridViewIcon.addEventListener('click', function() {
            content.classList.remove('list-view');
            content.style.display = 'flex';
            treeView.style.display = 'none';
            mapContainer.style.display = 'none';
            sidebar.style.display = 'none';
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => card.classList.remove('list-view'));
            document.querySelector('.list-view-table').style.display = 'none';
            document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'block');
        });

        listViewIcon.addEventListener('click', function() {
            content.classList.add('list-view');
            content.style.display = 'none';
            treeView.style.display = 'none';
            mapContainer.style.display = 'none';
            sidebar.style.display = 'none';
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => card.classList.add('list-view'));
            document.querySelector('.list-view-table').style.display = 'table';
            document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'block');
        });

        treeViewIcon.addEventListener('click', function() {
            content.style.display = 'none';
            treeView.style.display = 'block';
            mapContainer.style.display = 'none';
            sidebar.style.display = 'none';
            document.querySelector('.list-view-table').style.display = 'none';
            document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'none');
        });

        mapViewIcon.addEventListener('click', function() {
            content.style.display = 'none';
            treeView.style.display = 'none';
            mapContainer.style.display = 'block';
            sidebar.style.display = 'none';
            document.querySelector('.list-view-table').style.display = 'none';
            document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'none');
            if (!map) { // Check if map is already initialized
                map = L.map('map', {
                    scrollWheelZoom: false
                }).setView([-0.789275, 113.921327], 4.35);

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

                map.on('keydown', function(e) {
                    if (e.originalEvent.ctrlKey || e.originalEvent.metaKey) { // Enable for both Ctrl (Windows) and Command (Mac)
                        map.scrollWheelZoom.enable();
                    }
                });

                map.on('keyup', function(e) {
                    if (!e.originalEvent.ctrlKey && !e.originalEvent.metaKey) { // Disable for both Ctrl (Windows) and Command (Mac)
                        map.scrollWheelZoom.disable();
                    }
                });

                var googleSat = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}',{
                    maxZoom: 20,
                    subdomains:['mt0','mt1','mt2','mt3']
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

                function getColor(count, source) {
                    if (source === 'kupunesia') {
                        return 'rgba(128, 0, 128, 0.5)'; // Warna ungu untuk kupunesia
                    }
                    return count > 50 ? 'rgba(128, 0, 38, 0.5)' :
                           count > 20 ? 'rgba(189, 0, 38, 0.5)' :
                           count > 10 ? 'rgba(227, 26, 28, 0.5)' :
                           count > 5  ? 'rgba(252, 78, 42, 0.5)' :
                           count > 2  ? 'rgba(253, 141, 60, 0.5)' :
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

                @foreach($checklists as $checklist)
                    var lat = Math.floor({{ $checklist->latitude }} / gridSize) * gridSize;
                    var lng = Math.floor({{ $checklist->longitude }} / gridSize) * gridSize;
                    var key = lat + ',' + lng;

                    if (!grid[key]) {
                        grid[key] = { count: 0, source: '{{ $checklist->source }}' };
                    }
                    grid[key].count++;

                    // Tambahkan ke grid besar
                    var largeLat = Math.floor({{ $checklist->latitude }} / largeGridSize) * largeGridSize;
                    var largeLng = Math.floor({{ $checklist->longitude }} / largeGridSize) * largeGridSize;
                    var largeKey = largeLat + ',' + largeLng;

                    if (!largeGrid[largeKey]) {
                        largeGrid[largeKey] = { count: 0, source: '{{ $checklist->source }}' };
                    }
                    largeGrid[largeKey].count++;

                    // Tambahkan ke grid ekstra besar
                    var extraLargeLat = Math.floor({{ $checklist->latitude }} / extraLargeGridSize) * extraLargeGridSize;
                    var extraLargeLng = Math.floor({{ $checklist->longitude }} / extraLargeGridSize) * extraLargeGridSize;
                    var extraLargeKey = extraLargeLat + ',' + extraLargeLng;

                    if (!extraLargeGrid[extraLargeKey]) {
                        extraLargeGrid[extraLargeKey] = { count: 0, source: '{{ $checklist->source }}' };
                    }
                    extraLargeGrid[extraLargeKey].count++;

                    // Tambahkan ke grid ultra besar
                    var ultraLargeLat = Math.floor({{ $checklist->latitude }} / ultraLargeGridSize) * ultraLargeGridSize;
                    var ultraLargeLng = Math.floor({{ $checklist->longitude }} / ultraLargeGridSize) * ultraLargeGridSize;
                    var ultraLargeKey = ultraLargeLat + ',' + ultraLargeLng;

                    if (!ultraLargeGrid[ultraLargeKey]) {
                        ultraLargeGrid[ultraLargeKey] = { count: 0, source: '{{ $checklist->source }}' };
                    }
                    ultraLargeGrid[ultraLargeKey].count++;

                    checklists.push({
                        id: {{ $checklist->id }},
                        lat: {{ $checklist->latitude }},
                        lng: {{ $checklist->longitude }},
                        date: "{{ $checklist->created_at }}", // Add date
                        source: "{{ $checklist->source }}" // Add source
                    });
                @endforeach

                var rectangles = {}; // Object to store rectangles with unique IDs
                var largeRectangles = {}; // Object to store large rectangles
                var extraLargeRectangles = {}; // Object to store extra large rectangles
                var ultraLargeRectangles = {}; // Object to store ultra large rectangles
                var markers = []; // Array to store markers

                function showData(lat, lng, gridSize) {
                    // Filter checklists based on the clicked grid's lat and lng
                    const filteredChecklists = checklists.filter(checklist => {
                        return Math.floor(checklist.lat / gridSize) * gridSize === lat &&
                               Math.floor(checklist.lng / gridSize) * gridSize === lng;
                    });

                    // Clear previous content
                    sidebarContent.innerHTML = '';

                    // Add new content
                    filteredChecklists.forEach(checklist => {
                        const checklistElement = document.createElement('div');
                        checklistElement.innerHTML = `
                            <p><strong>ID:</strong> ${checklist.id}</p>
                            <p><strong>Lokasi:</strong> ${checklist.lat}, ${checklist.lng}</p>
                            <p><strong>Date:</strong> ${checklist.date}</p>
                            <p><strong>Source:</strong> ${checklist.source}</p>
                        `;
                        sidebarContent.appendChild(checklistElement);
                    });

                    // Show the sidebar
                    sidebar.style.display = 'block';
                }

                // Event listener untuk menutup sidebar
                closeSidebarButton.addEventListener('click', function() {
                    sidebar.style.display = 'none';
                });

                function addGrid(opacity = 1, level = 1) {
                    var size = level === 2 ? gridSize * 0.99 : (level === 3 ? gridSize / 4 : gridSize);
                    for (var key in grid) {
                        var parts = key.split(',');
                        var lat = parseFloat(parts[0]);
                        var lng = parseFloat(parts[1]);
                        var count = grid[key].count;
                        var source = grid[key].source;

                        var bounds = [
                            [lat, lng],
                            [lat + size, lng + size]
                        ];

                        var rectangle = L.rectangle(bounds, {
                            color: getColor(count, source),
                            opacity: opacity,
                            fillColor: getColor(count, source),
                            fillOpacity: opacity,
                            weight: 1
                        }).addTo(map);

                        // Assign a unique ID to the rectangle
                        var rectId = 'rect-' + lat + '-' + lng + '-' + size;
                        rectangles[rectId] = rectangle;

                        rectangle.setStyle({ opacity: 0, fillOpacity: 0 });

                        // Add click event listener to rectangle
                        (function(lat, lng, gridSize) {
                            rectangle.on('click', function() {
                                showData(lat, lng, gridSize);
                            });
                        })(lat, lng, gridSize);
                    }
                }

                function addLargeGrid(opacity = 1) {
                    for (var key in largeGrid) {
                        var parts = key.split(',');
                        var lat = parseFloat(parts[0]);
                        var lng = parseFloat(parts[1]);
                        var count = largeGrid[key].count;
                        var source = largeGrid[key].source;

                        var bounds = [
                            [lat , lng],
                            [lat + largeGridSize, lng + largeGridSize]
                        ];

                        var rectangle = L.rectangle(bounds, {
                            color: getColor(count, source),
                            opacity: opacity,
                            fillColor: getColor(count, source),
                            fillOpacity: opacity,
                            weight: 1,
                            className: 'large-grid'
                        }).addTo(map);

                        // Assign a unique ID to the rectangle
                        var rectId = 'large-rect-' + lat + '-' + lng + largeGridSize;
                        largeRectangles[rectId] = rectangle;

                        rectangle.setStyle({ opacity: 0, fillOpacity: 0 });

                        // Add click event listener to rectangle
                        (function(lat, lng, gridSize) {
                            rectangle.on('click', function() {
                                showData(lat, lng, largeGridSize);
                            });
                        })(lat, lng, largeGridSize);
                    }
                }

                function addExtraLargeGrid(opacity = 1) {
                    for (var key in extraLargeGrid) {
                        var parts = key.split(',');
                        var lat = parseFloat(parts[0]);
                        var lng = parseFloat(parts[1]);
                        var count = extraLargeGrid[key].count;
                        var source = extraLargeGrid[key].source;

                        var bounds = [
                            [lat , lng],
                            [lat + extraLargeGridSize, lng + extraLargeGridSize]
                        ];

                        var rectangle = L.rectangle(bounds, {
                            color: getColor(count, source),
                            opacity: opacity,
                            fillColor: getColor(count, source),
                            fillOpacity: opacity,
                            weight: 1,
                            className: 'extra-large-grid'
                        }).addTo(map);

                        // Assign a unique ID to the rectangle
                        var rectId = 'extra-large-rect-' + lat + '-' + lng + extraLargeGridSize;
                        extraLargeRectangles[rectId] = rectangle;

                        rectangle.setStyle({ opacity: 0, fillOpacity: 0 });

                        // Add click event listener to rectangle
                        (function(lat, lng, gridSize) {
                            rectangle.on('click', function() {
                                showData(lat, lng, extraLargeGridSize);
                            });
                        })(lat, lng, extraLargeGridSize);
                    }
                }

                function addUltraLargeGrid(opacity = 1) {
                    for (var key in ultraLargeGrid) {
                        var parts = key.split(',');
                        var lat = parseFloat(parts[0]);
                        var lng = parseFloat(parts[1]);
                        var count = ultraLargeGrid[key].count;
                        var source = ultraLargeGrid[key].source;

                        var bounds = [
                            [lat , lng],
                            [lat + ultraLargeGridSize, lng + ultraLargeGridSize]
                        ];

                        var rectangle = L.rectangle(bounds, {
                            color: getColor(count, source),
                            opacity: opacity,
                            fillColor: getColor(count, source),
                            fillOpacity: opacity,
                            weight: 1,
                            className: 'ultra-large-grid'
                        }).addTo(map);

                        // Assign a unique ID to the rectangle
                        var rectId = 'ultra-large-rect-' + lat + '-' + lng + ultraLargeGridSize;
                        ultraLargeRectangles[rectId] = rectangle;

                        // Add click event listener to rectangle
                        (function(lat, lng, gridSize) {
                            rectangle.on('click', function() {
                                showData(lat, lng, ultraLargeGridSize);
                            });
                        })(lat, lng, ultraLargeGridSize);
                    }
                }

                function addMarkers() {
                    checklists.forEach(function(checklist) {
                        var bounds = [
                            [checklist.lat - 0.005, checklist.lng - 0.005],
                            [checklist.lat + 0.005, checklist.lng + 0.005]
                        ];
                        var rectangle = L.rectangle(bounds, {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            weight: 1
                        }).addTo(map)
                        // .bindPopup("NameId: " + checklist.nameId + "<br>Observer: " + checklist.observer + "<br>Date: " + checklist.date);
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
                        });

                        map.on('zoomend', function() {
                            var zoomLevel = map.getZoom();
                            var opacity = zoomLevel > 11 ? 0.9 : 1;
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
                        });

                        // Pindahkan tooltip ke pojok kiri bawah
                        zoomTooltip.setLatLng(map.getBounds().getSouthWest()).addTo(map);

                        var customAttribution = L.control.attribution({
                            position: 'bottomright'
                        });

                        customAttribution.onAdd = function (map) {
                            var div = L.DomUtil.create('div', 'custom-attribution');
                            div.innerHTML = '<img src="{{ asset('storage/icon/FOBI.png') }}" alt="Logo" style="width:auto;height:16px;">'; return div;
                        };

                        customAttribution.addTo(map);
                        }
                        });
                        });
</script>

                <body>
    <header class="p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="{{ asset('storage/icon/FOBI.png') }}" alt="Fobi Logo">
            </div>
             <!-- Large Screen Navigation Bar -->
    <nav>
        <div class="links">
            <ul class="nav">
          <p><a href="#">Jelajahi</a></p>
          <p><a href="#">Eksplorasi Saya</a></p>
          <p><a href="{{ route('bantu_identifikasi.index') }}">Bantu Ident</a></p>
          <p><a href="#">Komunitas</a></p>
          <div class="dropdown d-none d-md-inline" style="position: relative; top: 55px; left:-5%;">
            <button class="dropdown-toggle btn btn-transparent" type="button" id="dropdownMenuButtonA" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonA">
              <a class="dropdown-item" href="#">Artikel</a>
              <a class="dropdown-item" href="#">Forum</a>
              <a class="dropdown-item" href="#">Kontributor</a>
            </div>
            {{-- <p><div class="dropdown" style="position: relative; top: 55px; left:-10%;">
              <button class="dropdown-toggle btn btn-transparent" type="button" id="dropdownMenuButtonA" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonA">
                <a class="dropdown-item" href="#">Artikel</a>
                <a class="dropdown-item" href="#">Forum</a>
                <a class="dropdown-item" href="#">Kontributor</a>
            </div>

            </div></p> --}}
          </ul>

          <strong id="mobile-menu-btn"><i class="fa fa-bars"></i></strong>
        </div>
      </nav>
      <div class="user-info">
        <span style="position: relative; bottom: 5px;">
            <i class="fas fa-user-circle d-none d-md-inline" style="font-size: 30px;"></i>
        </span>
        @auth
            <span>{{ Auth::user()->uname }}</span>
        @else
            <span><a href="{{ route('login') }}">Login</a></span>
        @endauth
        <i class="fa fa-bell d-none d-md-inline"></i>
        <i class="fa fa-envelope d-none d-md-inline"></i>
        <span><strong style="font-size: 18px;">120</strong><br><small>Observasi</small></span>
        <div class="dropdown d-md-none">
            <button class="btn btn-transparent" style="position: relative; bottom: 10px; right: 15px;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle" style="font-size: 20px; position: relative;  right: 10px;"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a>
                <a class="dropdown-item" href="#"><i class="fa fa-bell"></i> Notifikasi</a>
                <a class="dropdown-item" href="#"><i class="fa fa-envelope"></i> Pesan</a>
            </div>
        </div>
    </div>

    <!-- Small Screen Navigation Bar -->
      <div class="mobile-menu">
        @if(auth()->check())
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Signup</a>
        @endif
        <a href="#">Jelajahi</a>
        <a href="#">Eksplorasi Saya</a>
        <a href="#">Bantu Ident</a>
        <a href="#">Komunitas</a>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var mobileMenuBtn = document.querySelector("#mobile-menu-btn");
          var mobileMenu = document.querySelector(".mobile-menu");

          if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener("click", (event) => {
              event.stopPropagation();
              if (mobileMenu.style.display === "none" || mobileMenu.style.display === "") {
                mobileMenu.style.display = "flex";
                mobileMenuBtn.innerHTML = "<i class='fa fa-times'></i>";
              } else {
                mobileMenu.style.display = "none";
                mobileMenuBtn.innerHTML = "<i class='fa fa-bars'></i>";
              }
            });

            // Close menu when clicking outside
            document.addEventListener("click", (event) => {
              if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                mobileMenu.style.display = "none";
                mobileMenuBtn.innerHTML = "<i class='fa fa-bars'></i>";
              }
            });

            // Close menu when pressing Escape
            document.addEventListener("keydown", (event) => {
              if (event.key === "Escape") {
                mobileMenu.style.display = "none";
                mobileMenuBtn.innerHTML = "<i class='fa fa-bars'></i>";
              }
            });
          }
          // Tambahkan event listener untuk scroll
          window.addEventListener('scroll', function() {
                        var header = document.querySelector('header');
                        if (window.scrollY > 0) {
                            header.classList.add('fixed-header');
                            document.body.classList.add('fixed-header-padding');
                        } else {
                            header.classList.remove('fixed-header');
                            document.body.classList.remove('fixed-header-padding');
                        }
                    });
        });
      </script>
        </header>

        <div class="d-flex justify-content-between align-items-center p-2 w-100" style="background-color: #679995;">
            <div class="filter d-flex align-items-center">
                <input type="text" class="search-box me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Spesies/genus/famili">
                <input type="text" class="search-box me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Lokasi">
                <button class="btn-warning border-0"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                <button class="btn-warning border-0">Filter</button>
            </div>
            <div class="stats row align-items-center text-center w-100">
                <div class="col-md-2 col-6 mb-2"><strong class="text-md-left text-warning" style="font-size: 14px;">500.345</strong> <br> <small style="font-size: 12px;">OBSERVASI</small></div>
                <div class="col-md-2 col-6 mb-2"><strong class="text-md-left text-warning" style="font-size: 14px;">230.478</strong> <br> <small style="font-size: 12px;">BURUNGNESIA</small></div>
                <div class="col-md-2 col-6 mb-2"><strong class="text-md-left text-warning" style="font-size: 14px;">30.592</strong> <br> <small style="font-size: 12px;">KUPUNESIA</small></div>
                <div class="col-md-2 col-6 mb-2"><strong class="text-md-left text-warning" style="font-size: 14px;">3.987</strong> <br> <small style="font-size: 12px;">FOTO & AUDIO</small></div>
                <div class="col-md-2 col-6 mb-2"><strong class="text-md-left text-warning" style="font-size: 14px;">1.345</strong> <br> <small style="font-size: 12px;">SPESIES (Tree)</small></div>
                <div class="col-md-2 col-6 mb-2"><strong class="text-md-left text-warning" style="font-size: 14px;">678</strong> <br> <small style="font-size: 12px;">KONTRIBUTOR</small></div>
            </div>
        </div>
    <main class="container my-1 mb-5 w-100">

        <div class="d-flex justify-content-between align-items-center my-4">
            <div class="observasi-new">
                <h5 class="list-view-only">Observasi Terbaru</h5>
            </div>

            <div class="view-icons">
                <i class="fa fa-map" style="color:#555;" aria-hidden="true"></i>
                <i class="fas fa-th-large" style="color:#555;"></i>
                <i class="fas fa-th-list" style="color:#555;"></i>
                <img class="tree-icon img-fluid" style="height: 25px;" src="{{ asset('storage/icon/TREE.png') }}" alt="Tree Icon">
            </div>
        </div>
        <div id="map">
            <div id="sidebar" class="sidebar">
                <button id="close-sidebar" class="close-sidebar">X</button>
                <div id="sidebar-content" class="sidebar-content">
                    <!-- Konten sidebar akan ditambahkan di sini melalui JavaScript -->
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Biru-laut ekor-blorok">
                <div class="card-body">
                    <h5 class="card-title">Biru-laut ekor-blorok</h5>
                    <p class="card-text">Bar-Tailed Godwit<br><i>Limosa lapponica</i><br><i>Limosa</i><br><i>Scolopacidae</i>
                    <span class="badge" style="position: relative; left: 4.375rem; bottom: 1.2rem;"><img src="{{ asset('storage/icon/label.png') }}" alt="check" style="width: auto; height: 11px;"></span>
                    <span class="badge" style="position: relative; left: 1.3rem;"><img src="{{ asset('storage/icon/label.png') }}" alt="check" style="width: auto; height: 11px;"></span>
                    </p>
                </div>
                <div class="card-footer">
                    <span>Sikebo</span>
                    <span>16 Burungnesia</span>
                </div>
            </div>
            <div class="card">
                <img src="{{ asset('storage/icon/btt.jpg') }}" alt="Bentet">
                <div class="card-body">
                    <h5 class="card-title">Bentet</h5>
                    <p class="card-text">Shrike<br><i>Lanius sp</i><br><i>Laniidae</i></p>
                </div>
                <div class="card-footer">
                    <span>Sikebo</span>
                    <span>13 Burungnesia</span>
                </div>
            </div>
            <div class="card">
                <img src="{{ asset('storage/icon/birur.jpeg') }}" alt="Kumbang biru">
                <div class="card-body">
                    <h5 class="card-title">Kumbang biru</h5>
                    <p class="card-text">Blue Tiger Bug<br><i>Mimo ilinus</i><br><i>Genus</i><br><i>Famili</i></p>
                </div>
                <div class="card-footer">
                    <span>Sikebo</span>
                    <span>11 Kupunesia</span>
                </div>
            </div>
            <div class="card">
                <img src="{{ asset('storage/icon/d.png') }}" alt="Ngengat darah">
                <div class="card-body">
                    <h5 class="card-title">Ngengat darah</h5>
                    <p class="card-text">Bloody Moth<br><i>Dyscophora rubicunda</i><br><i>Saturniidae</i></p>
                </div>
                <div class="card-footer">
                    <span>Sikebo</span>
                    <span>12 Kupunesia</span>
                </div>
            </div>
            <div class="card">
                <img src="{{ asset('storage/icon/ng.jpeg') }}" alt="Ngengat">
                <div class="card-body">
                    <h5 class="card-title">Ngengat</h5>
                    <p class="card-text">Moth<br><i>Actias sp</i><br><i>Saturniidae</i></p>
                </div>
                <div class="card-footer">
                    <span>Sikebo</span>
                    <span>10 Kupunesia</span>
                </div>
            </div>
        </div>

        <!-- Tabel untuk tampilan list view -->
        <table class="list-view-table table table-hover" style="display: none;">
            <thead>
                <tr>
                    <th></th>
                    <th>Verifikasi</th>
                    <th>Nama</th>
                    <th>Pengamat</th>
                    <th>Lokasi</th>
                    <th>Jumlah observasi</th>
                    <th>Tgl Observasi</th>
                    <th>Informasi tambahan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td data-label=""><img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Biru-laut ekor-blorok"></td>
                    <td data-label="Verifikasi" class="label"><img src="{{ asset('storage/icon/label.png') }}" alt="check"></td>
                    <td data-label="Nama">Biru-laut ekor-blorok<br>Bar-Tailed Godwit<br><i>Limosa lapponica</i></td>
                    <td data-label="Pengamat">Sikebo</td>
                    <td data-label="Lokasi">Serangan, Bali</td>
                    <td data-label="Jumlah observasi">13 FOBI<br>145 Burungnesia</td>
                    <td data-label="Tgl Observasi">23 Agustus 2024</td>
                    <td data-label="Informasi tambahan">Jenis kelamin<br>Umur<br>Berkembangbiak<br>Mengasuh</td>
                </tr>
                <tr>
                    <td data-label=""><img src="{{ asset('storage/icon/btt.jpg') }}" alt="Bentet"></td>
                    <td data-label="Verifikasi" class="label"><img src="{{ asset('storage/icon/label.png') }}" alt="check"></td>
                    <td data-label="Nama">Bentet<br>Shrike<br><i>Lanius sp</i></td>
                    <td data-label="Pengamat">Sikebo</td>
                    <td data-label="Lokasi">Serangan, Bali</td>
                    <td data-label="Jumlah observasi">13 FOBI<br>145 Burungnesia</td>
                    <td data-label="Tgl Observasi">23 Agustus 2024</td>
                    <td data-label="Informasi tambahan">Umur</td>
                </tr>
                <tr>
                    <td data-label=""><img src="{{ asset('storage/icon/birur.jpeg') }}" alt="Kumbang biru"></td>
                    <td data-label="Verifikasi" class="label"><img src="{{ asset('storage/icon/label.png') }}" alt="check"></td>
                    <td data-label="Nama">Kumbang biru<br>Blue Tiger Bug<br><i>Mimo ilinus</i></td>
                    <td data-label="Pengamat">Sikebo</td>
                    <td data-label="Lokasi">Serangan, Bali</td>
                    <td data-label="Jumlah observasi">13 FOBI<br>145 Kupunesia</td>
                    <td data-label="Tgl Observasi">23 Agustus 2024</td>
                    <td data-label="Informasi tambahan">Umur</td>
                </tr>
                <tr>
                    <td data-label=""><img src="{{ asset('storage/icon/d.png') }}" alt="Ngengat darah"></td>
                    <td data-label="Verifikasi" class="label"><img src="{{ asset('storage/icon/label.png') }}" alt="check"></td>
                    <td data-label="Nama">Ngengat darah<br>Bloody Moth<br><i>Dyscophora rubicunda</i></td>
                    <td data-label="Pengamat">Sikebo</td>
                    <td data-label="Lokasi">Serangan, Bali</td>
                    <td data-label="Jumlah observasi">13 FOBI<br>145 Kupunesia</td>
                    <td data-label="Tgl Observasi">23 Agustus 2024</td>
                    <td data-label="Informasi tambahan">Jenis suara</td>
                </tr>
                <tr>
                    <td data-label=""><img src="{{ asset('storage/icon/ng.jpeg') }}" alt="Ngengat"></td>
                    <td data-label="Verifikasi" class="label"><img src="{{ asset('storage/icon/label.png') }}" alt="check"></td>
                    <td data-label="Nama">Ngengat<br>Moth<br><i>Actias sp</i></td>
                    <td data-label="Pengamat">Sikebo</td>
                    <td data-label="Lokasi">Serangan, Bali</td>
                    <td data-label="Jumlah observasi">13 FOBI<br>145 Kupunesia</td>
                    <td data-label="Tgl Observasi">23 Agustus 2024</td>
                    <td data-label="Informasi tambahan">Daur hidup<br>Tanaman inang</td>
                </tr>
            </tbody>
        </table>
        @php
        $faunasByOrdo = $faunas->groupBy(function($fauna) use ($orderFaunas) {
            return $orderFaunas->sortBy('ordo_order')->get($fauna->family)->ordo ?? 'Unknown Ordo';
        })->sortBy(function($faunas, $ordo) use ($orderFaunas) {
            return $orderFaunas->firstWhere('ordo', $ordo)->ordo_order ?? PHP_INT_MAX;
        });

        $selectedOrdo = request()->get('ordo');
        @endphp

 <!-- Tampilan pohon -->
 <div class="tree-view" style="display: none;">
    <ul>
        <li>
            <details>
                <summary>Life</summary>
                <ul>
                    <li>
                        <details>
                            <summary>Animalia</summary>
                            <ul>
                                @foreach($taxontest->where('kingdom', 'Animalia')->groupBy('phylum') as $phylum => $animalsByPhylum)
                                <li>
                                    <details>
                                        <summary>{{ $phylum }}</summary>
                                        <ul>
                                            @foreach($animalsByPhylum->groupBy('class') as $class => $animalsByClass)
                                                <li>
                                                    <details>
                                                        <summary>{{ $class }}</summary>
                                                        <ul>
                                                            @foreach($animalsByClass->groupBy('order') as $order => $animalsByOrder)
                                                            <li>
                                                                <details>
                                                                    <summary>{{ $order }}</summary>
                                                                    <ul>
                                                                        @foreach($animalsByOrder->groupBy('family') as $family => $animalsByFamily)
                                                                            <li>
                                                                                <details>
                                                                                    <summary>{{ $family }}</summary>
                                                                                    <ul>
                                                                                        @foreach($animalsByFamily->groupBy('genus') as $genus => $animalsByGenus)
                                                                                            <li>
                                                                                                <details>
                                                                                                    <summary>{{ $genus }}</summary>
                                                                                                    <ul>
                                                                                                        @foreach($animalsByGenus as $animal)
                                                                                                            <li>{{ $animal->cnameSpecies }} ({{ $animal->species }})</li>
                                                                                                        @endforeach
                                                                                                    </ul>
                                                                                                </details>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </details>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </details>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </li>
                            @endforeach
                            <li>
                                <details>
                                    <summary>Chordata</summary>
                                        <ul>
                                            <li>
                                                <details>
                                                    <summary>Aves</summary>
                                                    <ul>
                                                        @foreach($faunasByOrdo as $ordo => $faunas)
                                                            <li style="{{ $ordo === 'Unknown Ordo' ? 'display:none;' : '' }}">
                                                                <details>
                                                                    <summary>{{ ucfirst(strtolower($ordo)) }}</summary>
                                                                    <ul>
                                                                        @foreach($faunas->groupBy('family') as $family => $faunaGroup)
                                                                            <li>
                                                                                <details>
                                                                                    <summary>{{ $family }}</summary>
                                                                                    <ul>
                                                                                        @foreach($faunaGroup as $fauna)
                                                                                            <li>
                                                                                                <a href="#">{{ $fauna->nameId }}</a>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </details>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </details>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            </li>
                                        </ul>
                                    </details>
                                </li>
                            </ul>
                        </details>
                    </li>
                    <li>
                        <details>
                            <summary>Plantae</summary>
                            <ul>
                                @foreach($taxontest->where('kingdom', 'Plantae')->groupBy('phylum') as $phylum => $plantsByPhylum)
                                    <li>
                                        <details>
                                            <summary>{{ $phylum }}</summary>
                                            <ul>
                                                @foreach($plantsByPhylum->groupBy('class') as $class => $plantsByClass)
                                                    <li>
                                                        <details>
                                                            <summary>{{ $class }}</summary>
                                                            <ul>
                                                                @foreach($plantsByClass->groupBy('order') as $order => $plantsByOrder)
                                                <li>
                                                    <details>
                                                        <summary>{{ $order }}</summary>
                                                        <ul>
                                                            @foreach($plantsByOrder->groupBy('family') as $family => $plantsByFamily)
                                                                <li>
                                                                    <details>
                                                                        <summary>{{ $family }}</summary>
                                                                        <ul>
                                                                            @foreach($plantsByFamily->groupBy('genus') as $genus => $plantsByGenus)
                                                                                <li>
                                                                                    <details>
                                                                                        <summary>{{ $genus }}</summary>
                                                                                        <ul>
                                                                                            @foreach($plantsByGenus as $plant)
                                                                                                <li>{{ $plant->cnameSpecies }} ({{ $plant->species }})</li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    </details>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </details>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </details>
                                                </li>
                                            @endforeach
                                                            </ul>
                                                        </details>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </li>
                </ul>
            </details>
        </li>
    </ul>
</div><script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleOrdoButton = document.getElementById('toggle-ordo');
        const ordoList = document.getElementById('ordo-list');

        toggleOrdoButton.addEventListener('click', function() {
            if (ordoList.style.display === 'none' || ordoList.style.display === '') {
                ordoList.style.display = 'block';
                toggleOrdoButton.textContent = '-';
            } else {
                ordoList.style.display = 'none';
                toggleOrdoButton.textContent = '+';
            }
        });
    });
</script>
</div>
    </main>
    {{-- <footer class="bg-light py-3">
        <p>&copy; 2024 Fobi</p>
    </footer> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.getElementById('dropdownMenuButtonA');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownToggle.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', function(event) {
                if (!dropdownMenu.contains(event.target) && !dropdownToggle.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>

</body>
</html>
