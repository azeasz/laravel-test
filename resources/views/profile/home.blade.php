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
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

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

        .profile-sidebar {
            padding: 20px 0 10px 0;
            background: #f0f0f0;
        }

        .profile-sidebar img {
    float: none;
    margin: 0 auto;
    width: 100px; /* Atur lebar sesuai kebutuhan */
    height: 100px; /* Atur tinggi sama dengan lebar */
    border-radius: 0; /* Hapus border-radius untuk membuat persegi */
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

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #000000;
        }
        .profile-header img {
            border-radius: 50%;
            width: auto;
            height: 25px;
        }

        .profile-bio h5 {
            border-bottom: 1px solid #000000;
        }
        .profile-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .profile-stats div {
            text-align: center;
        }
        .profile-activities {
            margin-top: 20px;
        }
        .profile-activities h5 {
            margin-bottom: 20px;
            border-bottom: 1px solid #000000;
        }
        .profile-following {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .profile-following h5 {
            border-bottom: 1px solid #000000;
        }
        .profile-following img {
            border-radius: 50%;
            margin-right: 10px;
        }
        .profile-followed {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .profile-followed h5 {
            border-bottom: 1px solid #000000;
        }
        .profile-followed img {
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

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }

        .profile-public {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-public img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }

        .profile-public h2 {
            margin-top: 10px;
            font-size: 24px;
            color: #333;
        }

        .profile-public p {
            font-size: 14px;
            color: #666;
        }

        .profile-public .stats {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 20px;
        }

        .profile-public .stats div {
            text-align: center;
        }

        .profile-public .stats div p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .profile-public .stats div span {
            font-size: 12px;
            color: #666;
        }

        .profile-public .follow-section {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 20px;
        }

        .profile-public .follow-section div {
            text-align: center;
        }

        .profile-public .follow-section div img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .profile-public .follow-section div p {
            margin: 0;
            font-size: 12px;
            color: #333;
        }
        .cke_notification {
        display: none;
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
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('storage/icon/user.png') }}" alt="User Image" width="100"><br>
                        <strong>{{ $user->fname }} {{ $user->lname }}</strong>
                        <small>{{ $user->uname }}</small>
                        <span class="badge rounded-pill bg-primary">
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
                            <li class="nav-item active">
                                <a class="nav-link" href="#">
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
                <div class="profile-header text-md-center">
                    <div class="date text-center mb-1 mt-3">
                        <span style="margin-left: 10px; font-size: 12px;">Tanggal bergabung: {{ $user->created_at->format('d F Y') }}</span>
                        <span style="margin-left: 30px; font-size: 12px;">Terakhir aktif: {{ $user->updated_at->format('d F Y') }}</span>
                    </div>
                </div>

                <!-- Bagian Bio -->
                <div class="profile-bio mt-4">
                    <h5>Profile</h5>
                    <p id="bio-text">
                        {!! $user->bio ?? 'FOBI adalah komunitas yang mengedepankan kejujuran dan kepercayaan antar anggota karena informasi yang termuat dalam FOBI mempunyai pertanggungjawaban secara ilmiah. Terkadang orang lain perlu mengetahui latar belakang anda untuk menaruh kepercayaan akan observasi atau bantuan identifikasi dari anda.' !!}
                    </p>
                    <button class="btn-success shadow-none border-0" style="background-color: #679995; font-size: 12px; color: #fff; padding: 5px 10px; margin: 5px;" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>

                    <!-- Modal -->
                    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="uname">Username:</label>
                                            <input type="text" name="uname" class="form-control" value="{{ $user->uname }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="profile_picture">Profile Picture:</label>
                                            <input type="file" name="profile_picture" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="bio">Bio:</label>
                                            <textarea id="bio" name="bio" class="form-control">{{ $user->bio }}</textarea>
                                        </div>
                                        <script>
                                            CKEDITOR.replace('bio');
                                        </script>
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>                </div>
                <div class="profile-activities">
                    <h5>Aktifitas saya</h5>
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                    <div class="time-range-buttons">
                        <button class="btn-secondary shadow-none border-0" style="background-color: #c0c0c0; font-size: 12px; color: #000; padding: 5px 10px; margin: 5px;" onclick="updateChart('year')">1 tahun terakhir</button>
                        <button class="btn-secondary shadow-none border-0" style="background-color: #c0c0c0; font-size: 12px; color: #000; padding: 5px 10px; margin: 5px;" onclick="updateChart('month')">1 bulan terakhir</button>
                        <button class="btn-secondary shadow-none border-0" style="background-color: #c0c0c0; font-size: 12px; color: #000; padding: 5px 10px; margin: 5px;" onclick="updateChart('week')">1 minggu terakhir</button>
                    </div>
                </div>
                <div class="profile-activities">
                    <h5>5 Taksa Teratas Observasi Saya</h5>
                    <div class="d-flex justify-content-between">
                        @foreach($topTaxaObservations as $taxa => $count)
                            <div>
                                <p>{{ $taxa }}</p>
                                <p>{{ $count }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="profile-activities">
                    <h5>5 Taksa Teratas Identifikasi Saya</h5>
                    <div class="d-flex justify-content-between">
                        @foreach($topTaxaIdentifications as $taxa => $count)
                            <div>
                                <p>{{ $taxa }}</p>
                                <p>{{ $count }}</p>
                            </div>
                        @endforeach
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <script>
        const ctx = document.getElementById('activityChart').getContext('2d');
        let activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // Labels akan diupdate berdasarkan pilihan
                datasets: [{
                    label: 'Observasi',
                    data: [],
                    borderColor: 'blue',
                    borderWidth: 1,
                    fill: true,
                    backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    pointRadius: 0.5,
                    pointHoverRadius: 10,
                    pointHitRadius: 10,
                }, {
                    label: 'Identifikasi',
                    data: [],
                    borderColor: 'orange',
                    borderWidth: 1,
                    fill: true,
                    backgroundColor: 'rgba(255, 165, 0, 0.1)',
                    pointRadius: 0.5,
                    pointHoverRadius: 10,
                    pointHitRadius: 10,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 40 // Menyesuaikan tinggi sumbu Y
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' observasi';
                                }
                                return label;
                            }
                        }
                    },
                }
            }
        });

        function updateChart(range) {
            let labels, dataObservasi, dataIdentifikasi;
            if (range === 'year') {
                labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                dataObservasi = [12, 19, 3, 5, 2, 3, 10, 15, 20, 25, 30, 35];
                dataIdentifikasi = [2, 3, 20, 5, 1, 4, 8, 12, 18, 22, 28, 32];
            } else if (range === 'month') {
                labels = Array.from({length: 30}, (_, i) => i + 1);
                dataObservasi = Array.from({length: 30}, () => Math.floor(Math.random() * 40));
                dataIdentifikasi = Array.from({length: 30}, () => Math.floor(Math.random() * 40));
            } else if (range === 'week') {
                labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                dataObservasi = Array.from({length: 7}, () => Math.floor(Math.random() * 40));
                dataIdentifikasi = Array.from({length: 7}, () => Math.floor(Math.random() * 40));
            }

            activityChart.data.labels = labels;
            activityChart.data.datasets[0].data = dataObservasi;
            activityChart.data.datasets[1].data = dataIdentifikasi;
            activityChart.update();
        }

        // Inisialisasi chart dengan data 1 tahun terakhir
        updateChart('year');
    </script>
</body>
</html>
