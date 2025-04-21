<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <title>Bantu Identifikasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
            padding-bottom: 20px;
            border-left: 1px solid black;
            bottom: 15px;
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
        .grid-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }
        .grid-item {
            background-color: white;
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: black;
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


        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
            .filter {
                flex-direction: column;
                align-items: flex-start;
            }
            .filter input, .filter button {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .grid-container {
                grid-template-columns: 1fr;
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
        <div class="filter d-flex d-inline-flex align-items-center mb-3">
            <input type="text" class="search-box me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Spesies/genus/famili">
            <input type="text" class="search-box me-2" style="background-color: rgba(14, 14, 14, 0.776); opacity: 0.5;" placeholder="Lokasi">
            <button class="btn-warning border-0"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
            <button class="btn-warning border-0">Filter</button>
        </div>

        <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="quality" id="quality1" value="option1">
                <label class="form-check-label" for="quality1">Kurang</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="quality" id="quality2" value="option2">
                <label class="form-check-label" for="quality2">Bantu Ident</label>
            </div>
        <div class="grid-container">
            @for ($i = 0; $i < 30; $i++)
                <div class="grid-item">
                    <a href="{{ route('detail_identifikasi.show', ['id' => $i]) }}">
                        <img src="{{ asset('storage/icon/icon.png') }}" alt="Observation Image" style="width: auto; height: 100px;">
                    </a>
                    <p>Nama ilmiah</p>
                    <p>Nama ilmiah</p>
                    <p>Beri catatan atau deskripsi</p>
                    <button class="btn btn-success btn-sm">Setuju</button>
                    <button class="btn btn-warning btn-sm">Tolak</button>
                </div>
            @endfor
        </div>
        <div class="pagination">
            <a href="#">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#">6</a>
            <a href="#">...</a>
            <a href="#">1000</a>
            <a href="#">Selanjutnya</a>
        </div>
    </div>
</body>
</html>
