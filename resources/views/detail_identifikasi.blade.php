<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Identifikasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@2.3.3/dist/esri-leaflet-geocoder.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
            transition: top 0.3s;
            border-bottom: 5px solid #679995;

        }
        .fixed-header {
            position: fixed;
            top: 0;
            border-bottom: 5px solid #679995;
        }
        body.fixed-header-padding {
            padding-top: 90px;
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
        .user-info i {
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
        .mobile-menu a {
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
            body {
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
        .container {
            width: 90%;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        .header img {
            height: 50px;
        }
        .header nav {
            display: flex;
            gap: 20px;
        }
        .header nav a {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }
        .detail-section {
            display: flex;
            margin: 20px 0;
        }
        .detail-section .image {
            width: 50%;
        }
        #map {
            width: 100%;
            height: 400px;
        }
        .detail-section .info {
            width: 50%;
            padding: 20px;
        }
        .scrollable {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .comment {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .comment img {
            border-radius: 50%;
            margin-right: 10px;
        }
        .comment button {
            margin-left: auto;
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

       .nav-link {
            position: relative;
            margin: 0;
        }

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }


        @media (max-width: 768px) {
            .detail-section {
                flex-direction: column;
            }
            .detail-section .image, .detail-section .info {
                width: 100%;
            }
        }

        .toolbar button {
        margin-right: 5px;
    }
    #editor {
        border: 1px solid #ccc;
        padding: 10px;
    }

    .btn-light.active {
                            background-color: #d1e7dd;
                            color: #0f5132;
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

    .modal {
        z-index: 10000;
    }


    </style>
</head>
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
          <p><a href="#">Bantu Ident</a></p>
          <p><a href="#">Komunitas</a></p>
          <div class="dropdown d-none d-md-inline" style="position: relative; top: 55px; left:-5%;">
            <button class="dropdown-toggle btn btn-transparent" type="button" id="dropdownMenuButtonA" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonA">
              <a class="dropdown-item" href="#">Artikel</a>
              <a class="dropdown-item" href="#">Forum</a>
              <a class="dropdown-item" href="#">Kontributor</a>
            </div>
          </ul>

          <strong id="mobile-menu-btn"><i class="fa fa-bars"></i></strong>
        </div>
      </nav>
      <div class="user-info">
        <i class="fa fa-bell d-none d-md-inline"></i>
        <i class="fa fa-envelope d-none d-md-inline"></i>

        <span style="position: relative; bottom: 5px;">
            <i class="fas fa-user-circle d-none d-md-inline" style="font-size: 30px;"></i>
        </span>

        <div class="dropdown d-md-none">
            <button class="btn btn-transparent" style="position: relative; bottom: 10px; right: 15px;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle" style="font-size: 30px; position: relative;  right: 1px;"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#"><i class="fa fa-bell"></i> Notifikasi</a>
                <a class="dropdown-item" href="#"><i class="fa fa-envelope"></i> Pesan</a>
                <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a>
            </div>
        </div>
        <button class="btn-success shadow-none border-0" style="background-color: #679995; font-size: 14px; color: white;"><a href="{{ route('profile.pilih_observasi') }}" style="color:white">Observasi Baru</a></button>

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
    <div class="container mt-5">
        <div class="detail-section">
            <div class="image mt-4">
                <img src="{{ asset('storage/icon/btt.jpg') }}" alt="Sample Image" style="width: 100%;">
                <div>
                    <p>Nama ilmiah</p>
                    <p>Lokasi</p>
                    <p>Tanggal</p>
                </div>
            </div>
            <div class="info">
                <div id="map">
                    <div id="sidebar" class="sidebar">
                        <button id="close-sidebar" class="close-sidebar">X</button>
                        <div id="sidebar-content" class="sidebar-content">
                            <!-- Konten sidebar akan ditambahkan di sini melalui JavaScript -->
                        </div>
                    </div>
                </div>
                <div id="map">
                    <div id="sidebar" class="sidebar">
                        <button id="close-sidebar" class="close-sidebar">X</button>
                        <div id="sidebar-content" class="sidebar-content">
                            <!-- Konten sidebar akan ditambahkan di sini melalui JavaScript -->
                        </div>
                    </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let map; // Declare map variable here

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
                            var closeSidebarButton = document.getElementById('close-sidebar');
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
                </script>

                <div class="scrollable">
                    <div class="comment">
                        <img src="{{ asset('storage/icon/user.png') }}" alt="User 1" style="width: 40px;">
                        <div>
                            <p><strong>OkiRahma</strong></p>
                            <p>Nama usulan spesies A</p>
                            <p>Terlihat jelas ekornya berwarna belang gelap-terang.</p>
                        </div>
                        <button class="btn btn-success btn-sm">Setuju</button>
                    </div>
                    <div class="comment">
                        <img src="{{ asset('storage/icon/user.png') }}" alt="User 2" style="width: 40px;">
                        <div>
                            <p><strong>tukangNgesroh</strong></p>
                            <p>Nama usulan spesies B</p>
                            <p>Seperti mentog</p>
                        </div>
                        <button class="btn btn-success btn-sm">Setuju</button>
                    </div>
                </div>
                <div>
                    <input type="text" class="form-control mt-2" placeholder="Usul nama">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="no-suggestion">
                        <label class="form-check-label" for="no-suggestion">Centang jika anda tidak ingin usul nama</label>
                    </div>
                    {{-- <textarea class="form-control mt-2" placeholder="Beri catatan atau komentar"></textarea> --}}
                    <div class="toolbar mt-2">
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'bold')"><b>B</b></button>
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'italic')"><i>I</i></button>
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'underline')"><u>U</u></button>
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'strikeThrough')"><s>S</s></button>
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'insertUnorderedList')"><i class="fa fa-list-ul"></i></button>
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'insertOrderedList')"><i class="fa fa-list-ol"></i></button>
                        <button class="btn btn-light" onclick="toggleFormatText(this, 'createLink')"><i class="fa fa-link"></i></button>
                    </div>
                    <div contenteditable="true" id="editor" class="form-control mt-2" style="height: 150px; overflow-y: auto;" placeholder="Beri catatan atau komentar"></div>

                    <script>
                        function toggleFormatText(button, command) {
                            if (command === 'createLink') {
                                const url = prompt("Enter the link here: ", "http://");
                                document.execCommand(command, false, url);
                            } else {
                                document.execCommand(command, false, null);
                            }
                            button.classList.toggle('active');
                        }
                    </script>

                    <div class="mt-2">
                        <p>Sertakan foto pembanding untuk memperkuat argumen anda</p>
                        <button class="btn-secondary border-0 mt-2" disabled>Lampirkan foto</button>
                    </div>
                    <div class="mt-2">
<!-- Tambahkan link untuk memicu modal -->
<p>Apakah ada yang salah dengan konten ini? <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal">Laporkan!</a></p>

<!-- Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reportModalLabel">Laporkan Konten</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Bantu kami menjaga site ini tetap bersih dari konten/ informasi yang tidak sesuai dengan tujuan dan Kebijakan Komunitas FOBI. Ketidaksesuaian konten bisa dalam bentuk foto, audio atau deskripsi observasi</p>
        <form>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reportReason" id="spam" value="spam">
            <label class="form-check-label" for="spam">
              Spam
              <small>Foto/ audio bukan obyek biodiversitas. Terdapat link mencurigakan atau tidak jelas informasi isi tujuan</small>
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reportReason" id="inappropriate" value="inappropriate">
            <label class="form-check-label" for="inappropriate">
              Tidak layak
              <small>Mengandung unsur: pornografi, sara, kekerasan, dll</small>
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="reportReason" id="other" value="other">
            <label class="form-check-label" for="other">
              Lain-lain
              <small>Jika menurut anda tidak ada satupun dari kategori di atas, berikan penjelasan mengapa anda menganggap itu tidak layak ada di FOBI</small>
            </label>
          </div>
          <div class="form-group mt-2">
            <textarea class="form-control" id="reportDescription" rows="3" maxlength="255" placeholder="Jelaskan alasan laporan Anda"></textarea>
            <small class="form-text text-muted"><span id="charCount">0</span>/255</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary">Kirim</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const reportDescription = document.getElementById('reportDescription');
    const charCount = document.getElementById('charCount');

    reportDescription.addEventListener('input', function() {
      charCount.textContent = reportDescription.value.length;
    });
  });
</script>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
