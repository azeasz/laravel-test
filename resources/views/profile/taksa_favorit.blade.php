<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Taksa Favorit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
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

        /* .container {
            margin-top: 20px;
        } */

        .profile-sidebar {
            padding: 20px 0 10px 0;
            background: #f0f0f0;
        }

        .profile-sidebar img {
            float: none;
            margin: 0 auto;
            width: auto;
            height: 25px;
            border-radius: 50%;
        }

        .nav-link {
            position: relative;
            margin: 0;
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

        .content {
            padding: 20px;
            background: #f0f0f0;
            max-width: 80%;

        }

        .content h5 {
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 14px;
            color: #333333;
        }

        .content .form-check {
            margin-bottom: 10px;
        }

        .content .btn {
            background-color: #679995;
            color: #ffffff;
        }

        .observasi-section {
            margin-bottom: 30px;
        }

        .observasi-section h6 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .observasi-section .observasi-item {
            text-align: center;
            margin-bottom: 20px;
        }

        .observasi-section .observasi-item img {
            width: 100px;
            height: 100px;
            background-color: #dcdcdc;
            display: block;
            margin: 0 auto;
        }

        .observasi-section .observasi-item p {
            margin-top: 10px;
            font-size: 12px;
        }

        .toggle-form {
            cursor: pointer;
            color: #679995;
            text-decoration: underline;
        }

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }

        @media (max-width: 988px) {
            .content {
                max-width: 100%;
            }
            .user-info i{
                position: relative;
                top: 10px;
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
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
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
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.observasi') }}">
                                    Observasi saya
                                </a>
                            </li>
                            <li class="nav-item active">
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
            <div class="col-md-9">
                <div class="content">
                    <h5>Taksa favorit</h5>
                    <p>Taksa favorit adalah fitur FOBI untuk anda yang fokus pada studi taksa tertentu. Di halaman ini nanti anda akan mendapatkan update observasi terbaru sesuai dengan taksa yang anda sukai.</p>

                    @if($favoriteTaxa)
                        @foreach($favoriteTaxaObservations as $taxa => $observations)
                            @if(in_array($taxa, $favoriteTaxa))
                                <div class="observasi-section">
                                    <h6>{{ $taxa }} <a href="#" class="btn btn-sm btn-outline-secondary">Lihat Observasi</a></h6>
                                    <div class="row">
                                        @foreach($observations as $observation)
                                            <div class="col-md-3 observasi-item">
                                                <img src="{{ $observation->image_url }}" alt="Observasi Image">
                                                <p>{{ $observation->common_name }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-outline-secondary">Muat lebih</button>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    <div class="toggle-form mt-4">Pilih taksa</div>
                    <form id="taxa-form" action="{{ route('profile.taksa_favorit.store') }}" method="POST" style="display: none;">
                        @csrf
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="burung" value="Burung" {{ in_array('Burung', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="burung">Burung</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="mamalia" value="Mamalia" {{ in_array('Mamalia', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="mamalia">Mamalia</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="reptil" value="Reptil" {{ in_array('Reptil', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reptil">Reptil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="amphibi" value="Amphibi" {{ in_array('Amphibi', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="amphibi">Amphibi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="capung" value="Capung" {{ in_array('Capung', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="capung">Capung</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="kupu-kupu" value="Kupu-kupu" {{ in_array('Kupu-kupu', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="kupu-kupu">Kupu-kupu</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="ngengat" value="Ngengat" {{ in_array('Ngengat', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ngengat">Ngengat</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="laba-laba" value="Laba-laba" {{ in_array('Laba-laba', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="laba-laba">Laba-laba</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="serangga-umum" value="Serangga umum" {{ in_array('Serangga umum', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="serangga-umum">Serangga umum</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="antropoda" value="Antropoda" {{ in_array('Antropoda', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="antropoda">Antropoda</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="ikan" value="Ikan" {{ in_array('Ikan', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ikan">Ikan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="nudibranch" value="Nudibranch" {{ in_array('Nudibranch', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="nudibranch">Nudibranch</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="terumbu-karang" value="Terumbu karang" {{ in_array('Terumbu karang', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="terumbu-karang">Terumbu karang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="pohon-berkayu" value="Pohon berkayu" {{ in_array('Pohon berkayu', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pohon-berkayu">Pohon berkayu</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="anggrek" value="Anggrek" {{ in_array('Anggrek', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="anggrek">Anggrek</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="herba" value="Herba" {{ in_array('Herba', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="herba">Herba</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="paku-pakuan" value="Paku-pakuan" {{ in_array('Paku-pakuan', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="paku-pakuan">Paku-pakuan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="taksa[]" id="lumut" value="Lumut" {{ in_array('Lumut', $favoriteTaxa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="lumut">Lumut</label>
                        </div>
                        <button type="submit" class="btn mt-3">Simpan</button>
                    </form>

                    <form action="{{ route('profile.taksa_favorit.reset') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-danger">Reset Taksa Favorit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.toggle-form').addEventListener('click', function() {
            var form = document.getElementById('taxa-form');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
    </script>
</body>
</html>
