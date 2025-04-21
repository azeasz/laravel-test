<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Unggah Observasi Checklist Burungnesia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }

        .nav-link {
            position: relative;
            margin: 0;
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
            background: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        .upload-section {
            margin-bottom: 30px;
        }

        .upload-section .form-group {
            margin-bottom: 15px;
        }

        .upload-section .form-group label {
            font-weight: bold;
        }

        .upload-section .form-group input,
        .upload-section .form-group select,
        .upload-section .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #dcdcdc;
        }

        .media-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .media-item {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .media-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .media-item .btn-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
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
        <div class="row">
            <div class="col-md-3">
                <div class="profile-sidebar">
                    <div>
                        <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="100">
                        <strong>{{ $user->fname }} {{ $user->lname }}</strong>
                        <small>{{ $user->uname }}</small>
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
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    Spesies saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.diskusi_identifikasi') }}">
                                    Diskusi identifikasi
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ route('profile.pilih_observasi') }}">
                                    Unggah Observasi Baru
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="content">
                    <h5>Unggah Observasi Checklist Burungnesia</h5>
                    <form action="{{ route('fobi.upload.store_burungnesia') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="burungnesia">
                        <div class="upload-section">
                            <div class="form-group">
                                <label for="location">Lokasi</label>
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <input type="text" name="location" id="location" required readonly>
                                <button type="button" id="location-btn" class="btn btn-secondary mt-2">Pilih Lokasi</button>
                            </div>

                            <!-- Modal untuk memilih lokasi -->
                            <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true" style="z-index: 9999;">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="locationModalLabel">Pilih Lokasi</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="map" style="height: 400px;"></div>
                                            <div class="form-row mt-3">
                                                <div class="form-group col-md-3">
                                                    <label for="latitude">Latitude:</label>
                                                    <input type="text" class="form-control" id="latitude" readonly>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="longitude">Longitude:</label>
                                                    <input type="text" class="form-control" id="longitude" readonly>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="accuracy">Acc (m):</label>
                                                    <input type="text" class="form-control" id="accuracy" readonly>
                                                </div>
                                                <div class="form-group col-md-4 d-flex align-items-end">
                                                    <div class="w-100">
                                                        <label for="locality-notes">Locality notes:</label>
                                                        <input type="text" class="form-control" id="locality-notes" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="button" class="btn btn-primary" id="save-location">Simpan Lokasi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var map;
                                    var marker;
                                    var circle;

                                    // Tampilkan modal saat tombol diklik
                                    document.getElementById('location-btn').addEventListener('click', function() {
                                        $('#locationModal').modal('show');
                                    });

                                    // Inisialisasi peta saat modal dibuka
                                    $('#locationModal').on('shown.bs.modal', function () {
                                        if (!map) {
                                            map = L.map('map').setView([-0.2201, 113.8256], 5); // Koordinat awal

                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                maxZoom: 19,
                                            }).addTo(map);

                                            // Event untuk memilih lokasi
                                            map.on('click', function(e) {
                                                if (marker) {
                                                    map.removeLayer(marker);
                                                    map.removeLayer(circle);
                                                }
                                                marker = L.marker(e.latlng).addTo(map);
                                                circle = L.circle(e.latlng, { radius: 50 }).addTo(map); // Radius default 50 meter
                                                document.getElementById('latitude').value = e.latlng.lat;
                                                document.getElementById('longitude').value = e.latlng.lng;
                                                document.getElementById('accuracy').value = circle.getRadius(); // Akurasi berdasarkan radius

                                                // Reverse geocoding untuk mendapatkan nama lokasi
                                                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        document.getElementById('locality-notes').value = data.display_name;
                                                    })
                                                    .catch(error => console.error('Error:', error));
                                            });
                                        }
                                    });

                                    // Simpan lokasi ke input
                                    document.getElementById('save-location').addEventListener('click', function() {
    var latInput = document.getElementById('latitude').value;
    var lngInput = document.getElementById('longitude').value;
    var accInput = document.getElementById('accuracy').value;
    var localityNotesInput = document.getElementById('locality-notes').value;

    document.getElementById('location').value = latInput + ', ' + lngInput + ', ' + accInput + ', ' + localityNotesInput;

    $('#locationModal').modal('hide');
});
                                });
                            </script>

                            <div class="form-group">
                                <label for="date">Tanggal</label>
                                <input type="date" name="date" id="date" required>
                            </div>
                            <div class="form-group">
                                <label for="time_start">Waktu Mulai</label>
                                <input type="time" name="time_start" id="time_start" required>
                            </div>
                            <div class="form-group">
                                <label for="time_end">Waktu Selesai</label>
                                <input type="time" name="time_end" id="time_end" required>
                            </div>
                            <div class="form-group">
                                <label for="complete_checklist">Apakah checklist lengkap?</label>
                                <div>
                                    <input type="radio" name="complete_checklist" id="complete_yes" value="yes" required>
                                    <label for="complete_yes">Ya</label>
                                    <input type="radio" name="complete_checklist" id="complete_no" value="no" required>
                                    <label for="complete_no">Tidak</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="habitat">Habitat</label>
                                <input type="text" name="habitat" id="habitat" required>
                            </div>
                            <div class="form-group">
                                <label for="other_observers">Observer lain</label>
                                <input type="text" name="other_observers" id="other_observers">
                            </div>
                            <div class="form-group">
                                <label for="description">Keterangan</label>
                                <textarea name="description" id="description" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="upload-section">
                            <h6>Daftar Burung</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nama Ilmiah</th>
                                        <th>Jumlah</th>
                                        <th>Berbiak</th>
                                        <th>Perilaku Berbiak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bird-list">
                                    <!-- Daftar burung akan ditambahkan di sini -->
                                </tbody>
                            </table>
                            <button type="button" class="btn" id="add-bird">Tambah Jenis</button>
                        </div>

                        <!-- Modal untuk Tambah Jenis Burung -->
                        <div class="modal fade" id="addBirdModal" tabindex="-1" role="dialog" aria-labelledby="addBirdModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addBirdModalLabel">Tambah Jenis Burung</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="bird_species">Jenis Burung</label>
                                            <input type="text" name="bird_species" id="bird_species" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="bird_count">Jumlah Individu</label>
                                            <input type="number" name="bird_count" id="bird_count" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="bird_notes">Catatan</label>
                                            <textarea name="bird_notes" id="bird_notes" rows="2"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="bird_behavior">Apakah Berbiak?</label>
                                            <input type="checkbox" name="bird_behavior" id="bird_behavior">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-primary" id="save-bird">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal untuk Edit Jenis Burung -->
                        <div class="modal fade" id="editBirdModal" tabindex="-1" role="dialog" aria-labelledby="editBirdModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editBirdModalLabel">Edit Jenis Burung</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="edit_bird_species">Jenis Burung</label>
                                            <input type="text" name="edit_bird_species" id="edit_bird_species">
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_bird_count">Jumlah Individu</label>
                                            <input type="number" name="edit_bird_count" id="edit_bird_count">
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_bird_notes">Catatan</label>
                                            <textarea name="edit_bird_notes" id="edit_bird_notes" rows="2"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_bird_behavior">Apakah Berbiak?</label>
                                            <input type="checkbox" name="edit_bird_behavior" id="edit_bird_behavior">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-primary" id="update-bird">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal untuk Tambah Foto -->
                        <div class="modal fade" id="addPhotoModal" tabindex="-1" role="dialog" aria-labelledby="addPhotoModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="bird_photo">Unggah Foto</label>
                                            <input type="file" class="form-control" id="bird_photo" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-primary" id="save-photo">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.getElementById('add-bird').addEventListener('click', function() {
                                $('#addBirdModal').modal('show');
                            });

                            document.getElementById('save-bird').addEventListener('click', function() {
                                var birdList = document.getElementById('bird-list');
                                var birdSpecies = document.getElementById('bird_species').value;
                                var birdCount = document.getElementById('bird_count').value;
                                var birdNotes = document.getElementById('bird_notes').value;
                                var birdBehavior = document.getElementById('bird_behavior').checked ? 'Ya' : 'Tidak';

                                var birdItem = document.createElement('tr');
                                birdItem.classList.add('bird-item');
                                birdItem.innerHTML = `
                                    <td>
                                    </td>
                                    <td>${birdSpecies}</td>
                                    <td>${birdCount}</td>
                                    <td>${birdBehavior}</td>
                                    <td>${birdNotes}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-edit-bird">Edit</button>
                                        <button type="button" class="btn btn-danger btn-remove-bird">Hapus</button>
                                        <button type="button" class="btn btn-primary btn-add-photo">Tambah Foto</button>
                                    </td>
                                `;
                                birdList.appendChild(birdItem);

                                $('#addBirdModal').modal('hide');
                            });

                            document.getElementById('bird-list').addEventListener('click', function(event) {
                                if (event.target.classList.contains('btn-remove-bird')) {
                                    event.target.parentElement.parentElement.remove();
                                } else if (event.target.classList.contains('btn-edit-bird')) {
                                    var currentRow = event.target.parentElement.parentElement;
                                    document.getElementById('edit_bird_species').value = currentRow.cells[1].innerText;
                                    document.getElementById('edit_bird_count').value = currentRow.cells[2].innerText;
                                    document.getElementById('edit_bird_notes').value = currentRow.cells[4].innerText;
                                    document.getElementById('edit_bird_behavior').checked = currentRow.cells[3].innerText === 'Ya';
                                    $('#editBirdModal').data('currentRow', currentRow).modal('show');
                                } else if (event.target.classList.contains('btn-add-photo')) {
                                    var currentRow = event.target.parentElement.parentElement;
                                    $('#addPhotoModal').data('currentRow', currentRow).modal('show');
                                }
                            });
                            document.getElementById('update-bird').addEventListener('click', function() {
    var currentRow = $('#editBirdModal').data('currentRow');
    currentRow.cells[1].innerText = document.getElementById('edit_bird_species').value;
    currentRow.cells[2].innerText = document.getElementById('edit_bird_count').value;
    currentRow.cells[3].innerText = document.getElementById('edit_bird_notes').value;
    $('#editBirdModal').modal('hide');
});



                            document.getElementById('save-photo').addEventListener('click', function() {
                                var currentRow = $('#addPhotoModal').data('currentRow');
                                var photoInput = document.getElementById('bird_photo');
                                var reader = new FileReader();

                                reader.onload = function(e) {
                                    var img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.style.width = '50px';
                                    img.style.height = '50px';
                                    currentRow.cells[0].appendChild(img);
                                };

                                reader.readAsDataURL(photoInput.files[0]);
                                $('#addPhotoModal').modal('hide');
                            });
                        </script>

                        <button type="submit" class="btn">Unggah</button>
                    </form>
                </div>
            </div>
        </div>


    </div>
    </div>

</body>
</html>
