<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil: Spesies Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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

        .nav-link {
            position: relative;
            margin: 0;
        }

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }
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
        .tree {
            --spacing: 1.5rem;
            --radius: 10px;
            margin-top: 20px;
        }
        .tree li {
            display: block;
            position: relative;
            padding-left: calc(2 * var(--spacing) - var(--radius) - 2px);
        }
        .tree ul {
            margin-left: calc(var(--radius) - var(--spacing));
            padding-left: 0;
        }
        .tree ul li {
            border-left: 2px solid #ddd;
        }
        .tree ul li:last-child {
            border-color: transparent;
        }
        .tree ul li::before {
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
        .tree summary {
            display: block;
            cursor: pointer;
        }
        .tree summary::marker,
        .tree summary::-webkit-details-marker {
            display: none;
        }
        .tree summary:focus {
            outline: none;
        }
        .tree summary:focus-visible {
            outline: 1px dotted #000;
        }
        .tree li::after,
        .tree summary::before {
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
        .tree summary::before {
            z-index: 1;
        }
        .tree details[open] > summary::before {
            background-color: #679995;
        }
        .grid-view {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .grid-view .card {
            width: calc(25% - 20px);
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.3s;
            font-size: 12px;

        }
        .grid-view .card img {
            width: 100%;
            height: auto;
        }
        .grid-view .card-body {
            padding: 10px;
        }
        .grid-view .card-footer {
            padding: 10px;
            background-color: #f0f0f0;
            text-align: center;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.tree a');
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const nextUl = this.nextElementSibling;
                    if (nextUl) {
                        nextUl.classList.toggle('collapsed');
                        this.classList.toggle('expandable');
                        this.classList.toggle('collapsible');
                    }
                });
            });

            const gridViewIcon = document.querySelector('.fa-th-large');
            const treeViewIcon = document.querySelector('.tree-icon');
            const gridView = document.querySelector('.grid-view');
            const treeView = document.querySelector('.tree');

            gridViewIcon.addEventListener('click', function() {
                gridView.style.display = 'flex';
                treeView.style.display = 'none';
            });

            treeViewIcon.addEventListener('click', function() {
                gridView.style.display = 'none';
                treeView.style.display = 'block';
            });
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
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.observasi') }}">
                                    Observasi saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.taksa_favorit') }}">
                                    Taksa favorit
                                </a>
                            </li>
                            <li class="nav-item active">
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
            <div class="col-md-9 mb-5">
                <h5 style="border-bottom: 1px solid #000000; padding-bottom: 10px;">Observasi Saya</h5>
                <div class="view-icons mb-3 mt-3">
                    <i class="fas fa-th-large" style="color:#555;" id="grid-view-icon"></i>
                    <i class="fas fa-tree" style="color:#555;" id="tree-view-icon"></i>
                </div>

                <div class="grid-view" id="grid-view">
                    @foreach($faunas as $fauna)
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="font-size: 14px;">{{ $fauna->nameId }}</h5>
                                <p class="card-text">{{ $fauna->nameLat }}</p>
                                <p class="card-text">{{ $fauna->nameEn }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @php
                $faunasByOrdo = $faunas->groupBy(function($fauna) use ($orderFaunas) {
                    return $orderFaunas->sortBy('ordo_order')->get($fauna->family)->ordo ?? 'Unknown Ordo';
                })->sortBy(function($faunas, $ordo) use ($orderFaunas) {
                    return $orderFaunas->firstWhere('ordo', $ordo)->ordo_order ?? PHP_INT_MAX;
                });

                $selectedOrdo = request()->get('ordo');
                @endphp

                <div class="tree" id="tree-view" style="display: none;">
                    <ul>
                        <li>
                            <details>
                                <summary>Life</summary>
                                <ul>
                                    <li>
                                        <details>
                                            <summary>Animalia</summary>
                                            <ul>
                                                @if($checklistsAka->isNotEmpty())
                                                    <li>
                                                        <details>
                                                            <summary>Chordata</summary>
                                                            <ul>
                                                                <li>
                                                                    <details>
                                                                        <summary>Aves <span class="category-text text-muted"><small>(Class)</small></span></summary>
                                                                        <ul>
                                                                            @foreach($faunasByOrdo as $ordo => $faunas)
                                                                                @if($ordo !== 'Unknown Ordo')
                                                                                    <li>
                                                                                        <details>
                                                                                            <summary>{{ ucfirst(strtolower($ordo)) }} <span class="category-text text-muted"><small>(Order)</small></span> ({{ $faunas->count() }})</summary>
                                                                                            <ul>
                                                                                                @foreach($faunas->groupBy('family') as $family => $faunaGroup)
                                                                                                    @if($family)
                                                                                                        <li>
                                                                                                            <details>
                                                                                                                <summary>{{ $family }} <span class="category-text text-muted"><small>(Family)</small></span></summary>
                                                                                                                <ul>
                                                                                                                    @php
                                                                                                                        // Ambil semua genus dan spesies terkait dengan fauna
                                                                                                                        $genusData = [];
                                                                                                                        foreach ($faunaGroup as $fauna) {
                                                                                                                            $genusFaunas = \App\Models\GenusFauna::where('fauna_id', $fauna->id)->get();
                                                                                                                            foreach ($genusFaunas as $genusFauna) {
                                                                                                                                $genusData[$genusFauna->genus][] = $genusFauna; // Kelompokkan berdasarkan genus
                                                                                                                            }
                                                                                                                        }
                                                                                                                    @endphp

                                                                                                                    @foreach($genusData as $genus => $genusFaunas)
                                                                                                                        <li>
                                                                                                                            <details>
                                                                                                                                <summary>
                                                                                                                                    <a href="{{ route('genus.gallery', ['genus' => $genus]) }}">
                                                                                                                                        {{ $genus }} <span class="category-text text-muted"><small>(Genus)</small></span> ({{ count($genusFaunas) }})
                                                                                                                                    </a>
                                                                                                                                </summary>
                                                                                                                                <ul>
                                                                                                                                    @foreach($genusFaunas as $g)
                                                                                                                                        <li>
                                                                                                                                            <a href="#"><i>{{ $g->nameLat }}</i> <span class="category-text text-muted" style="font-size: 12px;">{{ $g->nameId }}</span> <span class="category-text text-muted"><small>(Species)</small></span></a>
                                                                                                                                        </li>
                                                                                                                                    @endforeach
                                                                                                                                </ul>
                                                                                                                            </details>
                                                                                                                        </li>
                                                                                                                    @endforeach
                                                                                                                </ul>
                                                                                                            </details>
                                                                                                        </li>
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </details>
                                                                                    </li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </details>                                                                </li>
                                                            </ul>
                                                        </details>
                                                    </li>
                                                @endif
                                                @if($checklistsKupnes->isNotEmpty())
                                                    <li>
                                                        <details>
                                                            <summary>Arthropoda</summary>
                                                            <ul>
                                                                <li>
                                                                    <details>
                                                                        <summary>Insecta</summary>
                                                                        <ul>
                                                                            <li>
                                                                                <details>
                                                                                    <summary>Lepidoptera</summary>
                                                                                    <ul>
                                                                                        @foreach($faunas as $fauna)
                                                                                            <li>{{ $fauna->nameId }} ({{ $fauna->nameLat }})</li>
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
                                                @endif
                                            </ul>
                                        </details>
                                    </li>
                                </ul>
                            </details>
                        </li>
                    </ul>
                </div>
                <script>
                    document.getElementById('grid-view-icon').addEventListener('click', function() {
                        document.getElementById('grid-view').style.display = 'flex';
                        document.getElementById('tree-view').style.display = 'none';
                    });

                    document.getElementById('tree-view-icon').addEventListener('click', function() {
                        document.getElementById('grid-view').style.display = 'none';
                        document.getElementById('tree-view').style.display = 'block';
                    });
                </script>            </div>
        </div>
    </div>
</body>
</html>
