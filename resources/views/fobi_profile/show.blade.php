<!-- resources/views/profile/show.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
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

        .dropdown-menu, .show{
            margin: 0;
            position: relative;
            bottom: 10px;
        }
        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }



        @media (max-width: 600px) {
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


        .container {
            width: 80%;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .profile-info {
            flex: 1;
        }

        .profile-stats {
            flex: 1;
            text-align: right;
        }

        .profile-follow {
            margin-top: 20px;
        }

        .following-list, .followers-list {
            display: flex;
            flex-wrap: wrap;
        }

        .following-item, .follower-item {
            width: 100px;
            text-align: center;
            margin: 10px;
        }

        .following-item img, .follower-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .profile-sidebar {
            width: 20%;
            float: left;
            margin-right: 20px;
        }

        .profile-main {
            width: 75%;
            float: left;
        }

        .profile-sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            position: relative;
            margin-bottom: 0;
        }

        .profile-sidebar ul li {
            position: relative;
            margin-bottom: 2px;
        }

        .profile-sidebar ul li a {
            text-decoration: none;
            color: #000;
            padding: 10px;
            display: block;
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .profile-sidebar ul li a:hover {
            background-color: #ddd;
        }

        .profile-details {
            margin-bottom: 20px;
        }

        .profile-details h2 {
            margin-top: 0;
        }

        .profile-details p {
            margin: 5px 0;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
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
        <span style="position: relative; bottom: 5px;"><i class="fas fa-user-circle d-none d-md-inline" style="font-size: 30px;"></i></span>
        <span>Sikebo</span>
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
        <a href="#">Login</a>
        <a href="#">Signup</a>
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

    <div class="container clearfix">
        <div class="profile-sidebar">
            <img src="path/to/profile-picture.jpg" alt="Foto Profil" style="width: 100%; border-radius: 50%;">
            <ul>
                <li><a href="#">Profil</a></li>
                <li><a href="#">Observasi</a></li>
                <li><a href="#">Spesies</a></li>
                <li><a href="#">Identifikasi</a></li>
            </ul>
        </div>
        <div class="profile-main">
            <div class="profile-header">
                <div class="profile-info">
                    <h1>Jokowi Mulyono</h1>
                    <p>tukangngesroh</p>
                    <p>Kurator</p>
                    <p>Bergabung: 12 Juli 2023</p>
                    <p>Aktif terakhir: 12 Agustus 2024</p>
                </div>
                <div class="profile-stats">
                    <p>Observasi: 12.000</p>
                    <p>Spesies: 120</p>
                    <p>Identifikasi: 1.400</p>
                </div>
            </div>
            <div class="profile-details">
                <p>lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.</p>
                <p>Link to my portfolio with animal photos: <a href="https://www.shutterstock.com/cs/g/Vendula+Odkaldalova?rid=281753982">https://www.shutterstock.com/cs/g/Vendula+Odkaldalova?rid=281753982</a></p>
            </div>
            <div class="profile-follow">
                <h5>Mengikuti 4 orang</h5>
                <div class="following-list">
                    {{-- @foreach($user->following as $following) --}}
                        <div class="following-item">
                            <img src="path/to/following-profile-picture.jpg" alt="">
                            <p>abc123</p>
                        </div>
                    {{-- @endforeach --}}
                </div>
                <h5>Diikuti 4 orang</h5>
                <div class="followers-list">
                    {{-- @foreach($user->followers as $follower) --}}
                        <div class="follower-item">
                            <img src="path/to/follower-profile-picture.jpg" alt="">
                            <p>abc123</p>
                        </div>
                    {{-- @endforeach --}}
                </div>
            </div>
        </div>
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
        });
    </script>

</body>
</html>
