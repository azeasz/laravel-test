<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Observasi Saya</title>
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
        /* Tambahkan CSS yang diperlukan di sini */
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
            border-bottom: 5px solid #679995;
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
            padding-top: 25px;
            padding-bottom: 25px;
            border-left: 1px solid black;
            bottom: 14px;
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
            .user-info{
                padding-left: 10px;
                border-left: 1px solid #000000;
                bottom: 10px;
                padding-top: 15px;
                padding-bottom: 15px;
            }
            .user-info i{
                position: relative;
                top: 10px;
            }
            .logo img{
                height: 50px;
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
            width: 10%;
            height: 25%;
            margin-right: 10px;
        }

        .filter ::placeholder {
            color: rgb(244, 244, 244);
            font-size: 12px;
        }

        .profile-sidebar {
            padding: 20px 0 10px 0;
            background: #f0f0f0;
        }

        .profile-sidebar img {
            float: none;
            margin: 0 auto;
            width: auto;
            height: 25px;;
            -webkit-border-radius: 50% !important;
            -moz-border-radius: 50% !important;
            border-radius: 50% !important;
        }

        .profile-usertitle {
            text-align: center;
            margin-top: 20px;
        }

        .profile-usertitle-name {
            color: #5a7391;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .profile-usermenu {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .profile-usermenu ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .profile-usermenu ul li {
            border-bottom: 1px solid #dcdcdc;
        }

        .profile-usermenu ul li:last-child {
            border-bottom: none;
        }

        .profile-usermenu ul li a {
            color: #679995;
            font-size: 14px;
            font-weight: 400;
            display: block;
            padding: 10px 15px;
        }

        .profile-usermenu ul li a:hover {
            background-color: #f6f9fb;
            color: #679995;
            text-decoration: none;
        }

        .profile-usermenu ul li.active a {
            color: #ffffff;
            background-color: #679995;
            border-left: 2px solid #679995;
        }

        .nav-link {
            position: relative;
            margin: 0;
        }


        #map {
            height: 400px;
            width: 100%;
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

       .table img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
            margin-right: 10px;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .table a {
            color: #007bff;
            text-decoration: none;
        }

        .table a:hover {
            text-decoration: underline;
        }
        /* Tambahkan CSS untuk tampilan grid */
        .content {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .content.list-view {
            flex-direction: column;
        }

        .card {
            width: 11rem;
            overflow: hidden;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
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

        .list-view-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .list-view-table tr {
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

        .label img {
            width: 50px;
            height: auto;
        }

        @media (max-width: 768px) {
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
            .pagination {
                display: flex;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 425px) {
            .user-info {
                gap: 5px;
                position: relative;
                right: 10px;
            }
            .user-info span {
                position: relative;
                right: 5px;
            }

            #mobile-menu-btn {
                right: 5px;
            }

            .filter {
                font-size: 10px;
            }

            .view-icons {
                font-size: 10px;
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

        .pagination {
    display: flex;
    justify-content: center;
    padding: 1rem 0;
}

.pagination .page-item .page-link {
    color: #679995;
    border: 1px solid #dee2e6;
    margin: 0 0.25rem;
    padding: 0.5rem 0.75rem; /* Adjust padding to control size */
    font-size: 1rem; /* Adjust font size */
}

.pagination .page-item.active .page-link {
    background-color: #679995;
    border-color: #679995;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link svg {
    width: 1em; /* Adjust width to control size */
    height: 1em; /* Adjust height to control size */
    display: none;
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
                            const filter = document.querySelector('.filter');
                            const tombol = document.querySelector('.tombol-cari');
                            const isi = document.querySelector('.isi');
                            const table = document.querySelector('.obs-tb');
                            const paginasi = document.querySelector('.paginasi');
                            let map; // Declare map variable here

                             // Display map by default
                        if (mapContainer) {
                            mapContainer.style.display = 'block';
                            filter.style.display = 'flex';
                            table.style.display = 'table';
                            paginasi.style.display = 'flex';
                            map = L.map('map', {
                                scrollWheelZoom: false
                            }).setView([-0.789275, 113.921327], 4.35);

                            L.control.scale({
                                position: 'bottomright',
                                imperial: false,
                            }).addTo(map);

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

                            document.addEventListener('keydown', function(e) {
                                if (e.ctrlKey || e.metaKey) {
                                    map.scrollWheelZoom.enable();
                                }
                            });

                            document.addEventListener('keyup', function(e) {
                                if (!e.ctrlKey && !e.metaKey) {
                                    map.scrollWheelZoom.disable();
                                }
                            });

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(map);

                            var circleMarkerOptions = {
                                radius: 4,
                                fillColor: "crimson",
                                color: "crimson",
                                weight: 1,
                                opacity: 0.7,
                                fillOpacity: 0.7
                            };

                            @foreach($observations as $observation)
                                L.circleMarker([{{ $observation->latitude }}, {{ $observation->longitude }}], circleMarkerOptions).addTo(map)
                                    .bindPopup('');
                            @endforeach
                        }
                            if (gridViewIcon) {
                                gridViewIcon.addEventListener('click', function() {
                                    if (content) {
                                        content.classList.remove('list-view');
                                        content.style.display = 'flex';
                                    }
                                    if (treeView) treeView.style.display = 'none';
                                    if (mapContainer) mapContainer.style.display = 'none';
                                    if (filter) filter.style.display = 'none';
                                    if (table) table.style.display = 'none';
                                    if (isi) isi.style.display = 'none';
                                    if (tombol) tombol.style.display = 'none';
                                    if (paginasi) paginasi.style.display = 'none';
                                    const cards = document.querySelectorAll('.card');
                                    cards.forEach(card => card.classList.remove('list-view'));
                                    const listViewTable = document.querySelector('.list-view-table');
                                    if (listViewTable) listViewTable.style.display = 'none';
                                    document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'block');
                                });
                            }

                            if (listViewIcon) {
                                listViewIcon.addEventListener('click', function() {
                                    if (content) content.classList.add('list-view');
                                    if (content) content.style.display = 'none';
                                    if (treeView) treeView.style.display = 'none';
                                    if (mapContainer) mapContainer.style.display = 'none';
                                    if (filter) filter.style.display = 'none';
                                    if (table) table.style.display = 'none';
                                    if (isi) isi.style.display = 'none';
                                    if (tombol) tombol.style.display = 'none';
                                    if (paginasi) paginasi.style.display = 'none';
                                    const cards = document.querySelectorAll('.card');
                                    cards.forEach(card => card.classList.add('list-view'));
                                    const listViewTable = document.querySelector('.list-view-table');
                                    if (listViewTable) listViewTable.style.display = 'table';
                                    document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'block');
                                });
                            }

                            if (treeViewIcon) {
                                treeViewIcon.addEventListener('click', function() {
                                    if (content) content.style.display = 'none';
                                    if (treeView) treeView.style.display = 'block';
                                    if (mapContainer) mapContainer.style.display = 'none';
                                    if (filter) filter.style.display = 'none';
                                    if (table) table.style.display = 'none';
                                    if (isi) isi.style.display = 'none';
                                    if (tombol) tombol.style.display = 'none';
                                    if (paginasi) paginasi.style.display = 'none';
                                    const listViewTable = document.querySelector('.list-view-table');
                                    if (listViewTable) listViewTable.style.display = 'none';
                                    document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'none');
                                });
                            }

                            if (mapViewIcon) {
                                mapViewIcon.addEventListener('click', function() {
                                    if (content) content.style.display = 'none';
                                    if (treeView) treeView.style.display = 'none';
                                    if (mapContainer) mapContainer.style.display = 'block';
                                    const listViewTable = document.querySelector('.list-view-table');
                                    if (listViewTable) listViewTable.style.display = 'none';
                                    if (filter) filter.style.display = 'flex';
                                    if (table) table.style.display = 'table';
                                    if (isi) isi.style.display = 'block';
                                    if (tombol) tombol.style.display = 'block';
                                    if (paginasi) paginasi.style.display = 'flex';
                                    document.querySelectorAll('.list-view-only').forEach(el => el.style.display = 'none');
                                    if (!map) { // Check if map is already initialized
                                        map = L.map('map', {
                                        scrollWheelZoom: false
                                    }).setView([-2.548926, 118.0148634], 5);

                                    L.control.scale({
                                position: 'bottomright' // Mengatur posisi kontrol skala di kanan bawah
                            }).addTo(map);

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

                                    document.addEventListener('keydown', function(e) {
                                        if (e.ctrlKey || e.metaKey) {
                                            map.scrollWheelZoom.enable();
                                        }
                                    });

                                    document.addEventListener('keyup', function(e) {
                                        if (!e.ctrlKey && !e.metaKey) {
                                            map.scrollWheelZoom.disable();
                                        }
                                    });

                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                    }).addTo(map);

                                    @foreach($observations as $observation)
                                        L.marker([{{ $observation->latitude }}, {{ $observation->longitude }}]).addTo(map)
                                            .bindPopup('');
                                    @endforeach
                                }
                            });
                        }
                    });
                </script>

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
        <a href="{{ route('bantu_identifikasi.index') }}">Bantu Ident</a>
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
        <div class="row">
            <div class="col-md-3">
                <div class="profile-sidebar">
                    <div>
                        <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="100">
                        <strong>{{ $user->fname }} {{ $user->lname }}</strong>
                        <small>{{ $user->uname }}</small>
                        <span class="badge bg-primary">
                            @php
                                $roles = [
                                    1 => 'User',
                                    2 => 'Kurator',
                                    3 => 'Admin',
                                    4 => 'Admin + Kurator'
                                ];
                            @endphp
                            {{ $roles[$user->level] ?? '' }}
                        </span>

                    </div>

                    <div class="profile-usermenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.home') }}">
                                    Profil
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ route('profile.observasi') }}">
                                    Observasi saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.taksa_favorit') }}">
                                    Taksa favorit
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.spesies_saya') }}">
                                    Spesies saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.diskusi_identifikasi') }}">
                                    Diskusi identifikasi
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9 mt-3">
                <h5 style="border-bottom: 1px solid #000000; padding-bottom: 10px;">Observasi Saya</h5>
                <div class="view-icons mb-3 mt-3">
                    <i class="fa fa-map" style="color:#555;" aria-hidden="true"></i>
                    <i class="fas fa-th-large" style="color:#555;"></i>
                    {{-- <i class="fas fa-th-list" style="color:#555;"></i> --}}
                    {{-- <img class="tree-icon img-fluid" style="height: 25px;" src="{{ asset('storage/icon/TREE.png') }}" alt="Tree Icon"> --}}
                </div>

                <div id="map" style="display: none;"></div>
                <div class="content" style="display: none;">
                    @foreach($observations as $observation)
                        <div class="card">
                            <img src="{{ asset('storage/observations/') }}" alt="Observation Image">
                            <div class="card-body">
                                {{-- <h5 class="card-title">{{ $observation->name }}</h5>
                                <p class="card-text">{{ $observation->nameLat }}</p>
                                <p class="card-text">{{ $observation->location }}</p> --}}
                            </div>
                            <div class="card-footer">
                                {{-- <span>{{ $observation->observation_date }}</span>
                                <span>{{ $observation->upload_date }}</span> --}}
                            </div>
                        </div>
                    @endforeach
                </div>
                <table class="list-view-table table table-hover" style="display: none; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tanggal Observasi</th>
                            <th>Tanggal Unggah</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($observations as $observation)
                            <tr>
                                <td>
                                    {{-- <img src="{{ asset('storage/observations/' . $observation->image) }}" alt="Observation Image"> --}}
                                    {{-- <div>{{ $observation->name }}</div> --}}
                                    {{-- <div><small>{{ $observation->nameLat }}</small></div> --}}
                                </td>
                                {{-- <td>{{ $observation->observation_date }}</td>
                                <td>{{ $observation->upload_date }}</td>
                                <td>{{ $observation->location }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="tree-view" style="display: none;">
                    <ul>
                        <li>
                            <details>
                                <summary>Life</summary>
                                <ul>
                                    <li>
                                        <details>
                                            <summary>Animalia</summary>
                                            <ul>
                                                <li>
                                                    <details>
                                                        <summary>Chordata</summary>
                                                        <ul>
                                                            <li>
                                                                <details>
                                                                    <summary>Aves</summary>
                                                                    <ul>
                                                                        @foreach($ordos as $ordo)
                                                                            <li>
                                                                                <details>
                                                                                    <summary>{{ ucfirst(strtolower($ordo->ordo)) }}</summary>
                                                                                    <ul>
                                                                                        @foreach($families as $family)
                                                                                            @if($family->ordo == $ordo->ordo)
                                                                                                <li>
                                                                                                    <details>
                                                                                                        <summary>{{ $family->family }}</summary>
                                                                                                        <ul>
                                                                                                            @foreach($observations as $observation)
                                                                                                                @if($observation->fauna && $observation->fauna->family == $family->family)
                                                                                                                    <li>
                                                                                                                        <a href="#">{{ $observation->fauna->nama_umum }}</a>
                                                                                                                    </li>
                                                                                                                @endif
                                                                                                            @endforeach
                                                                                                        </ul>
                                                                                                    </details>
                                                                                                </li>
                                                                                            @endif
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
                                </ul>
                            </details>
                        </li>
                    </ul>
                </div> --}}
                                <script>
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
                                <div class="filter d-flex align-items-center mt-3" style="display: none;">
                                    <input type="text" class="search-box isi me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Cari observasi saya...">
                                    <button class="btn-success border-0 tombol-cari"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                </div>

                                <table class="table obs-tb mt-3" style="display: none; font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 150px;">Nama</th>
                                            <th style="width: 130px;">Tanggal Observasi</th>
                                            <th style="width: 120px;">Tanggal Unggah</th>
                                            <th>Lokasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginatedObservations as $observation)
                                            <tr>
                                                <td>
                                                    {{-- <img src="{{ asset('storage/observations/' . $observation->image) }}" alt="Observation Image"> --}}
                                                    <div>{{ $observation->fauna_name }}</div>
                                                    <div><small>{{ $observation->fauna_family }}</small></div>
                                                    {{-- <span class="badge {{ $observation->status == 'Bantu Iden' ? 'badge-danger' : 'badge-success' }}">
                                                        {{ $observation->status }}
                                                    </span> --}}
                                                </td>
                                                <td>{{ $observation->tgl_pengamatan }}</td>
                                                <td>{{ $observation->updated_at }}</td>
                                                <td id="location-{{ $observation->id }}"></td>
                                                <td style="width: 120px;">
                                                    <a href="#">Edit</a> |
                                                    <a href="#">Lihat</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="paginasi justify-content-center mt-2 mb-3" style="display: none;">
                                    {{ $paginatedObservations->links('vendor.pagination.custom') }}
                                </div>

            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observations = @json($paginatedObservations->items()); // Pastikan ini adalah array

            // Jika observations adalah objek, akses array di dalamnya
            const observationArray = Array.isArray(observations) ? observations : Object.values(observations);

            observationArray.forEach(observation => {
                const lat = observation.latitude;
                const lon = observation.longitude;
                const elementId = `location-${observation.id}`;

                if (lat && lon) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            const locationName = data.display_name;
                            document.getElementById(elementId).innerText = locationName;
                        })
                        .catch(error => {
                            console.error('Error fetching location:', error);
                        });
                }
            });
        });
    </script>

</body>
</html>
