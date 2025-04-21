<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Identifikasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <p><a href="#">Bantu Ident</a></p>
                        <p><a href="#">Komunitas</a></p>
                        <div class="dropdown d-none d-md-inline" style="position: relative; top: 55px; left:-5%;">
                            <button class="dropdown-toggle btn btn-transparent" type="button" id="dropdownMenuButtonA" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
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
            <div class="mobile-menu">
                @if(auth()->check())
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
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

                        document.addEventListener("click", (event) => {
                            if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                                mobileMenu.style.display = "none";
                                mobileMenuBtn.innerHTML = "<i class='fa fa-bars'></i>";
                            }
                        });

                        document.addEventListener("keydown", (event) => {
                            if (event.key === "Escape") {
                                mobileMenu.style.display = "none";
                                mobileMenuBtn.innerHTML = "<i class='fa fa-bars'></i>";
                            }
                        });
                    }

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
        </div>
    </header>
    <div class="container mt-5">
        <div class="detail-section">
            <div class="image">
                <img src="{{ asset('storage/icon/sample.jpg') }}" alt="Sample Image" style="width: 100%;">
                <div>
                    <p>Nama ilmiah</p>
                    <p>Lokasi</p>
                    <p>Tanggal</p>
                </div>
            </div>
            <div class="info">
                <div id="map"></div>
                <div class="scrollable">
                    <div class="comment">
                        <img src="{{ asset('storage/icon/user1.jpg') }}" alt="User 1" style="width: 40px;">
                        <div>
                            <p><strong>OkiRahma</strong></p>
                            <p>Nama usulan spesies A</p>
                            <p>Terlihat jelas ekornya berwarna belang gelap-terang.</p>
                        </div>
                        <button class="btn btn-success btn-sm">Setuju</button>
                    </div>
                    <div class="comment">
                        <img src="{{ asset('storage/icon/user2.jpg') }}" alt="User 2" style="width: 40px;">
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
                    <textarea class="form-control mt-2" placeholder="Beri catatan atau komentar"></textarea>
                    <button class="btn btn-secondary mt-2">Lampirkan foto</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
