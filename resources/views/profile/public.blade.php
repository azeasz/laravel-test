<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@2.3.3/dist/esri-leaflet-geocoder.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>

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

        .profile-sidebar {
            padding: 20px 0 10px 0;
            background: #f0f0f0;
        }


        .profile-sidebar img {
            border-radius: 50%;
            margin-bottom: 10px;
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

        .profile-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #000000;
        }

        .profile-bio {
            background-color: #f0f0f0;
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-bio h5 {
            border-bottom: 1px solid #000000;
        }

        .profile-activities h5 {
            margin-bottom: 20px;
            border-bottom: 1px solid #000000;
        }

        .profile-following h5 {
            border-bottom: 1px solid #000000;
        }

        .profile-followed h5 {
            border-bottom: 1px solid #000000;
        }

        .profile-following, .profile-followed {
            background-color: #f0f0f0;
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-following img, .profile-followed img {
            border-radius: 50%;
            margin-right: 10px;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            margin: 0;
        }

        .time-range-buttons {
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .chart-container {
                margin:0;
            }

            .time-range-buttons {
                margin-top: 0;
                position: relative;
                top: -120px;
            }
            .user-info i{
                position: relative;
                top: 10px;
            }
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


        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }
        .dropdown-menu {
            margin: 0;
            padding: 0;
            gap: 0;
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
        <button class="btn-success shadow-none border-0" style="background-color: #679995; font-size: 14px; color: white;">Unggah</button>

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
        <div class="row">
            <div class="col-md-3">
                <div class="profile-sidebar">
                    <div>
                        <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="100">
                    </div>

                    <div class="profile-usermenu">
                        <ul class="nav flex-column">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">
                                    Profil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.public.observasi', ['uname' => $user->uname]) }}">
                                    Observasi <span class="badge bg-secondary">{{ $activities['observations'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    Spesies <span class="badge bg-secondary">{{ $topTaxaObservations['Burung'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    Identifikasi <span class="badge bg-secondary">{{ $activities['identifications'] }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="profile-header text-md-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="50" class="rounded-circle">
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $user->uname }}</h5>
                                <p class="mb-0">{{ $user->fname }} {{ $user->lname }}</p>
                            </div>
                        </div>
                        <div>
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
                    </div>
                    <div class="date text-center mb-1 mt-3">
                        <span style="margin-left: 10px; font-size: 12px;">Bergabung: {{ $user->created_at->format('d F Y') }}</span>
                        <span style="margin-left: 30px; font-size: 12px;">Aktif terakhir: {{ $user->updated_at->format('d F Y') }}</span>
                    </div>
                </div>

                <!-- Bagian Bio -->
                <div class="profile-bio mt-4">
                    <h5>Profile</h5>
                    <p id="bio-text">
                        {{ $user->bio ?? 'FOBI adalah komunitas yang mengedepankan kejujuran dan kepercayaan antar anggota karena informasi yang termuat dalam FOBI mempunyai pertanggungjawaban secara ilmiah. Terkadang orang lain perlu mengetahui latar belakang anda untuk menaruh kepercayaan akan observasi atau bantuan identifikasi dari anda.' }}
                    </p>
                </div>
                <div class="profile-following">
                    <h5 class="mb-3">Mengikuti {{ count($following) }} Orang</h5>
                    <div class="d-flex">
                        @foreach($following as $follow)
                            <div class="text-center">
                                <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="50">
                                <p>{{ $follow }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="profile-followed">
                    <h5 class="mb-3">Diikuti {{ count($followers) }} Orang</h5>
                    <div class="d-flex">
                        @foreach($followers as $follower)
                            <div class="text-center">
                                <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="50">
                                <p>{{ $follower }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
