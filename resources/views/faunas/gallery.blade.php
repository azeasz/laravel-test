<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Gallery(Spesies)</title>
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
        /* .gallery {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .gallery-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        } */

        /* .gallery-header .stats {
            display: flex;
            gap: 20px;
        }

        .gallery-header .stats div {
            text-align: center;
        } */

        .gallery-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .gallery-item {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fff;
            width: 200px;
            text-align: center;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
        }

        .similar-species, .map {
            margin-top: 20px;
            width: 100%;
        }

        .similar-species img, .map img {
            width: 100%;
            height: auto;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 10000;
        }

        .popup img {
            width: 100%;
            height: auto;
        }

        .popup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .filter-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .filter-container input, .filter-container button {
            margin: 0 10px;
            padding: 10px;
            font-size: 16px;
        }

        .filter-container button {
            background-color: #ffc107;
            border: none;
            cursor: pointer;
        }

        .filter-container button:hover {
            background-color: #e0a800;
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
            <div class="d-flex justify-content-between align-items-center p-2 w-100" style="background-color: #679995;">
                <div class="filter d-flex align-items-center">
                    <input type="text" class="search-box me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Spesies/genus/famili">
                    <input type="text" class="search-box me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Lokasi">
                    <button class="btn-warning border-0"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                    <button class="btn-warning border-0">Filter</button>
                </div>

                <div class="stats row align-items-center text-center w-100">
                    <div class="col-md-2 col-6 mb-2">
                        <strong class="text-md-left text-warning" style="font-size: 14px;">{{ $observations }}</strong><br>
                        <small style="font-size: 12px;">OBSERVASI</small>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <strong class="text-md-left text-warning" style="font-size: 14px;">{{ $checklist_burnes }}</strong><br>
                        <small style="font-size: 12px;">BURUNGNESIA</small>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <strong class="text-md-left text-warning" style="font-size: 14px;">{{ $checklist_kupnesia }}</strong><br>
                        <small style="font-size: 12px;">KUPUNESIA</small>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <strong class="text-md-left text-warning" style="font-size: 14px;">{{ $media }}</strong><br>
                        <small style="font-size: 12px;">FOTO & AUDIO</small>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <strong class="text-md-left text-warning" style="font-size: 14px;">{{ $species_tree }}</strong><br>
                        <small style="font-size: 12px;">SPESIES (Tree)</small>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <strong class="text-md-left text-warning" style="font-size: 14px;">{{ $contributors }}</strong><br>
                        <small style="font-size: 12px;">KONTRIBUTOR</small>
                    </div>
                </div>
            </div>

    <div class="container mt-3">
        <i class="text-secondary" style="font-size: 18px;">{{ $species }}</i><br>
        <small class="text-secondary" style="font-size: 18px;">Biru-laut ekor-blorok</small>
            <div class="gallery-content mt-5">
                @foreach ($gallery_items as $item)
                    <div class="gallery-item">
                        <img src="{{ $item['src'] }}" alt="{{ $item['alt'] }}">
                        <p>{{ $item['alt'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Tabs for Similar Species and Map -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="similar-species-tab" data-bs-toggle="tab" data-bs-target="#similar-species" type="button" role="tab" aria-controls="similar-species" aria-selected="true">Spesies Mirip</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="map-tab" data-bs-toggle="tab" data-bs-target="#map" type="button" role="tab" aria-controls="map" aria-selected="false">Peta</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="similar-species" role="tabpanel" aria-labelledby="similar-species-tab">
                    <div class="similar-species">
                        <div class="gallery-content">
                            @foreach ($similar_species as $species)
                                <div class="gallery-item">
                                    <img src="{{ $species['src'] }}" alt="{{ $species['alt'] }}">
                                    <p>{{ $species['alt'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
                    @include('faunas.partials.map')
                </div>
                        </div>
        </div>
    </div>
</div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mt-5">
                    <p>&copy; 2024 FOBI. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>


    <div class="popup" id="popup">
        <span class="close" onclick="closePopup()">&times;</span>
        <img src="path/to/large-image.jpg" alt="Large Image">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script>
        function openPopup(imageSrc) {
            const popup = document.getElementById('popup');
            popup.querySelector('img').src = imageSrc;
            popup.style.display = 'block';
        }

        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('.gallery-item img');
            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    openPopup(this.src);
                });
            });
        });
    </script>
</body>
</html>
