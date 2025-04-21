<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <title>Fobi</title>
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
            margin: 0;
            padding: 0;
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

        .observation-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }

        .card {
            border: 1px solid #ddd;
            margin: 20px 0;
            padding: 20px;
            background-color: #fff;
        }

        .card-body {
            padding: 20px;
        }

        .observation-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .observation-header img {
            margin-right: 10px;
        }

        .observation-header .user-info {
            border: none;
        }

        .observation-header .user-info h2 {
            margin: 0;
        }

        .observation-header .user-info p {
            margin: 0;
        }

        .observation-details {
            display: flex;
            gap: 0;
            align-items: center;
            margin-bottom: 20px;
            border: 1px solid #000000;
        }

        .observation-details img {
            width: 48%;
            height: auto;
        }

        .observation-details .map {
            width: 48%;
            height: 300px;
            border-left: 1px solid #000000;
        }

        .observation-location {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .observation-location i {
            color: red;
            margin-right: 5px;
        }

        .observation-thumbnails {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .observation-thumbnails img {
            width: 50px;
            height: 50px;
            margin-right: 5px;
        }

        .observation-comments {
            width: 100%;
            margin-top: 20px;
        }

        .observation-comments .comment {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .observation-comments .comment img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .observation-comments .comment .comment-text {
            flex: 1;
        }

        .observation-comments .comment .comment-text p {
            margin: 0;
        }

        .observation-comments .comment .comment-text small {
            color: #888;
        }

        .observation-form {
            width: 100%;
            margin-top: 20px;
        }

        .observation-form textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
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


        .observation-card {
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 20px;
            background-color: #fff;
            width: 90%;
        }

        .observation-card img {
            width: 100%;
            height: auto;
        }

        .observation-card .map {
            width: 100%;
            height: 300px;
        }

        .observation-location {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .observation-location i {
            color: red;
            margin-right: 5px;
        }

        .observation-thumbnails {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .observation-thumbnails img {
            width: 50px;
            height: 50px;
            margin-right: 5px;
        }

        .observation-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .observation-info .left, .observation-info .right {
            width: 48%;
        }

        .observation-info .right {
            text-align: right;
        }

        .observation-info .rating {
            display: flex;
            align-items: center;
            position: relative;
            left: 120px;
        }

        .observation-info .rating i {
            color: #ffc107;
            margin-right: 5px;
        }

        .observation-info .approved, .observation-info .rejected {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .observation-info .approved img, .observation-info .rejected img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .observation-info .approved span, .observation-info .rejected span {
            margin-right: 10px;
        }

        .observation-info .approved span:last-child, .observation-info .rejected span:last-child {
            margin-right: 0;
        }

        .suggestions-container {
        margin-top: 20px;
        position: relative;
        margin-right: 200px;
    }

    .suggestion-card {
        border: 1px solid #ddd;
        margin: 10px 0;
        padding: 10px;
        background-color: #fff;
        position: relative;
        left: 0;
        font-size: 15px;
    }

    .suggestion-card h4 {
        font-size: 15px;
    }

    .suggestion-card .user-info {
        display: flex;
        align-items: center;
    }

    .suggestion-card .user-info img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .suggestion-card .user-info h4 {
        margin: 0;
    }

    .suggestion-card .user-info p {
        margin: 0;
    }

    .suggestion-card .suggestion-details {
        margin-top: 10px;
    }

    .suggestion-card .suggestion-details .thumbnails {
        display: flex;
        margin-top: 10px;
    }

    .suggestion-card .suggestion-details .thumbnails img {
        width: 50px;
        height: 50px;
        margin-right: 5px;
    }

    .suggestion-card .suggestion-details .date {
        margin-top: 10px;
        color: #888;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }
            .suggestion-form {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            font-size: 15px;
        }

        .suggestion-form h4 {
            font-size: 15px;
        }

        .suggestion-form input[type="text"], .suggestion-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }

        .suggestion-form button {
            background-color: #ffc107;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .suggestion-form button:hover {
            background-color: #e0a800;
        }

        .suggestion-form .checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .suggestion-form .checkbox input {
            margin-right: 10px;
        }

        .suggestion-form .file-upload {
            margin-top: 10px;
        }

        .suggestion-form .file-upload input[type="file"] {
            display: none;
        }

        .suggestion-form .file-upload label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ffc107;
            cursor: pointer;
        }

        .suggestion-form .file-upload label:hover {
            background-color: #e0a800;
        }

        .report-link {
            margin-top: 10px;
            color: #888;
        }

        .report-link a {
            color: #888;
            text-decoration: none;
        }

        .report-link a:hover {
            text-decoration: underline;
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

       .text-editor {
        border: 1px solid #ddd;
        margin-bottom: 10px;
    }

    .toolbar {
        background-color: #f0f0f0;
        border-bottom: 1px solid #ddd;
        padding: 5px;
        display: flex;
        gap: 5px;
    }

    .toolbar button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
    }

    #editor {
        min-height: 100px;
        padding: 10px;
        outline: none;
    }

    #editor:empty:before {
        content: attr(placeholder);
        color: #888;
    }

    .modal {
        z-index: 10000;
        overflow-y: auto;
    }

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }

        @media (max-width: 600px) {
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

        .filter-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .observation-details {
            flex-direction: column;
        }

        .observation-details img, .observation-details .map {
            width: 100%;
        }

        .observation-info {
            flex-direction: column;
        }

        .observation-info .left, .observation-info .right {
            width: 100%;
            text-align: left;
        }

        .observation-info .rating {
            left: 0;
        }

        .suggestion-card .user-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .suggestion-card .user-info img {
            margin-bottom: 10px;
        }

        .suggestion-card .suggestion-details .thumbnails {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .observation-details {
            flex-direction: column;
        }

        .observation-details img, .observation-details .map {
            width: 100%;
        }

        .observation-info {
            flex-direction: column;
        }

        .observation-info .left, .observation-info .right {
            width: 100%;
            text-align: left;
        }

        .observation-info .rating {
            left: 0;
        }

        .suggestions-container{
            position: relative;
            left: 0;
            margin-right: 0;
        }

        .suggestion-card .user-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .suggestion-card .user-info img {
            margin-bottom: 10px;
        }

        .suggestion-card .suggestion-details .thumbnails {
            justify-content: center;
        }
    }
    </style>
</head>
<body>
    <header class="p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="{{ asset('storage/icon/FOBI.png') }}" alt="Fobi Logo">
            </div>
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
                        </div>
                    </ul>
                    <strong id="mobile-menu-btn"><i class="fa fa-bars"></i></strong>
                </div>
            </nav>
            <div class="user-info">
                <span style="position: relative; bottom: 5px;"><i class="fas fa-user-circle d-none d-md-inline" style="font-size: 30px;"></i></span>
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

    <div class="container">
        <div class="filter-container">
            <input type="text" placeholder="Spesies/genus/famili">
            <input type="text" placeholder="Lokasi">
            <button>Filter</button>
        </div>

        <div class="observation-container">
            <div class="observation-card border-0 shadow-none">
                <div class="observation-header" style="border: 1px solid #000000; background-color: #e9e9e976; position: relative; margin: 0;">
                    <img src="{{ asset('storage/icon/user.png') }}" alt="Foto Profil" style="width: auto; height: 30px; position: relative; left: 10px;">
                    <div class="user-info">
                        <h5>Sikebo</h5>
                        <p><a href="#">1.234 observasi</a></p>
                    </div>
                </div>
                <div class="observation-details">
                    <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Gambar Observasi" style="width: 48%; height: auto;">
                    <div class="map" id="map" style="width: 55%; height: 335px;"></div>
                </div>
                <div class="observation-thumbnails">
                    <img src="path/to/thumbnail1.jpg" alt="Thumbnail 1">
                    <img src="path/to/thumbnail2.jpg" alt="Thumbnail 2">
                    <img src="path/to/thumbnail3.jpg" alt="Thumbnail 3">
                </div>
                <div class="observation-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>lokasi</p>
                </div>
            </div>

            @if($observations->isNotEmpty())
            @foreach($observations as $observation)
                <div class="observation-info">
                    <div class="left">
                        <h4>Usulan nama oleh observer:</h4>
                        <h2><em>{{ $observation->species_name }}</em></h2>
                        <p>{{ $observation->common_name }}</p>
                        <p>{{ $observation->description }}</p>
                        <hr>
                        <p>Apakah identifikasi di atas sudah benar?</p>
                        <button class="btn-success">Setuju</button>
                        <hr>
                        <p>Disetujui oleh:</p>
                        <div class="approved">
                            @foreach($observation->approvedBy as $user)
                                <img src="{{ asset('storage/profiles/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                <span>{{ $user->name }}</span>
                            @endforeach
                        </div>
                        <p>Ditolak oleh:</p>
                        <div class="rejected">
                            @foreach($observation->rejectedBy as $user)
                                <img src="{{ asset('storage/profiles/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                <span>{{ $user->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="right">
                        <p>Observasi: <br> {{ $observation->observed_at->format('d F Y') }}</p>
                        <p>Unggah: <br> {{ $observation->uploaded_at->format('d F Y') }}</p>
                        <div class="rating">
                            <span>Rating:</span>
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa fa-star{{ $i < $observation->rating ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <p>Dinilai oleh {{ $observation->ratings_count }} pengguna</p>
                        <p>Dikirim melalui {{ $observation->source }}</p>
                        <a href="{{ route('observations.exif', $observation->id) }}">Exif data</a>
                    </div>
                </div>
                <div class="suggestions-container col-md-5">
                    @foreach($observation->suggestions as $suggestion)
                        <div class="suggestion-card">
                            <div class="user-info">
                                <img src="{{ asset('storage/profiles/' . $suggestion->user->profile_image) }}" alt="{{ $suggestion->user->name }}">
                                <div>
                                    <h4>{{ $suggestion->user->name }}</h4>
                                    <p>{{ $suggestion->name }}</p>
                                    <p>{{ $suggestion->comment }}</p>
                                    <p>{{ $suggestion->created_at->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <p>Tidak ada observasi yang ditemukan.</p>
        @endif

            <!-- Form untuk usulan nama -->
            <div class="suggestion-form">
                <h4>Bantu Sikebo memastikan identifikasinya, dengan memberi komentar, foto pembanding atau usul nama.</h4>
                <form action="{{ route('suggestions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="observation_id" value="{{ $observation->id }}">
                    <input type="text" name="name" placeholder="Usul nama" required>
                    <div class="checkbox">
                        <input type="checkbox" id="no-suggestion" name="no_suggestion">
                        <label for="no-suggestion">Centang jika anda tidak ingin usul nama</label>
                    </div>
                    <div class="text-editor">
                        <div class="toolbar">
                            <button type="button" onclick="formatText('bold')"><b>B</b></button>
                            <button type="button" onclick="formatText('italic')"><i>I</i></button>
                            <button type="button" onclick="formatText('underline')"><u>U</u></button>
                            <button type="button" onclick="formatText('insertOrderedList')">1.</button>
                            <button type="button" onclick="formatText('insertUnorderedList')">&bull;</button>
                            <button type="button" onclick="formatText('createLink')">&#128279;</button>
                        </div>
                        <div id="editor" contenteditable="true" placeholder="Beri catatan atau komentar"></div>
                        <textarea name="comment" style="display:none;"></textarea>
                    </div>
                    <div class="file-upload">
                        <input type="file" id="file-upload" name="photo">
                        <label for="file-upload">Lampirkan foto</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Usulan</button>
                </form>
                <div class="report-link">
                    <p>Apakah ada yang salah dengan konten ini? <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal">Laporkan!</a></p>
                </div>

            </div>
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
                        <div class="mb-3">
                            <label class="form-label">Spam</label>
                            <p>Foto/ audio bukan obyek biodiversitas. Terdapat link mencurigakan atau tidak jelas informasi isi tujuan</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tidak layak</label>
                            <p>Mengandung unsur: pornografi, sara, kekerasan, dll</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lain-lain</label>
                            <textarea class="form-control" id="reportReason" rows="3" placeholder="Jika menurut anda tidak ada satupun dari kategori di atas, berikan penjelasan mengapa anda menganggapnya itu tidak layak ada di FOBI"></textarea>
                            <small class="form-text text-muted">0/255</small>
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
</div>
</div>
<script>
    function formatText(command, value = null) {
        if (command === 'createLink') {
            value = prompt('Enter the link URL:');
        }
        document.execCommand(command, false, value);
    }
</script>
</div>

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

            var map = L.map('map').setView([-0.789275, 113.921327], 4.4);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        });
    </script>
</body>
</html>
