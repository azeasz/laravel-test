<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Spesies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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

        .species-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* .species-info {
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #fff;
} */

.species-info-card {
    display: flex;
            flex-direction: column;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 20px;
            background-color: #ffffff00;
            width: 100%;
        }

        .species-detail {
            display: flex;
            gap: 0;
            align-items: center;
            margin-bottom: 20px;
            border: 1px solid #000000;
        }

.species-info-card img {
    width: 48%;
    height: auto;
}

.species-thumbnail {
    position: relative;
    left: 10rem;
    justify-content: center;
}

.suggestion-photo {
    width: 50px;
    height: 50px;
    margin-right: 3px;
}

.map {
    width: 55%;
    height: 65.7vh;
    border-left: 1px solid #000000;
}

.location-info {
    text-align: center;
    position: relative;
    left: 15rem;
    bottom: 2rem;
    font-size: 12px;
    width: 97%;
}

.location-info i {
    color: red;
}

@media (max-width: 1100px) {
    .species-info-card {
        display: flex;
        flex-direction: column;
    }
    .species-info-card .species-detail {
        display: flex;
        flex-direction: column;
    }
    .species-info-card img {
        width: 100%; /* Sesuaikan lebar untuk layar kecil */
    }

    .species-thumbnail {
        left: 0; /* Atur ulang posisi untuk layar kecil */
        display: flex;
        justify-content: center;
    }

    .map {
        width: 100%; /* Sesuaikan lebar untuk layar kecil */
        height: 50vh; /* Sesuaikan tinggi untuk layar kecil */
        border-left: none; /* Hilangkan border kiri untuk layar kecil */
    }

    .location-info {
        left: 0; /* Atur ulang posisi untuk layar kecil */
        bottom: 0; /* Atur ulang posisi untuk layar kecil */
    }
}

#suggestionDisplay {
    border-bottom: 1px solid #000000;
}

#approvalDisplay {
    border-bottom: 1px solid #000000;
}

.rating-stars {
            display: flex;
            align-items: center;
        }
        .rating-stars span {
            font-size: 1.5rem;
            color: gold;
        }
        .profile-picture {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}


.user-info {
    display: flex;
    align-items: center;
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

       .cke_notification {
        display: none;
       }
       .saran-list {
    border: 1px solid #ccc;
    max-height: 150px;
    overflow-y: auto;
    position: absolute;
    background-color: white;
    width: 50%;
    z-index: 1000;
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.suggestion-item {
    padding: 10px;
    cursor: pointer;
}

.suggestion-item:hover {
    background-color: #f0f0f0;
}
.text-muted {
    color: #6c757d;
}

.s {
    text-decoration: line-through;
}
.ui-autocomplete {
        z-index: 10000;
        overflow-y: auto;
        max-height: 150px;
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
        <span><strong style="font-size: 18px;">{{ $total_observations }}</strong><br><small>Observasi</small></span>
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
        <div class="species-header">
            <div>
            <h4>{{ $species->nameLat }}</h4>
            <small>{{ $species->nameId }}</small>
            </div>
            <div>
                @auth
                <span class="badge bg-secondary">{{ auth()->user()->uname }}</span>
            @else
                <span class="badge bg-secondary">Tamu</span>
            @endauth
            <span>{{ $total_observations }} observasi</span>
                        </div>
        </div>
        <div class="species-info">
            <div class="species-info-card border-0 shadow-none">
                <div class="species-info-header">
                    @auth
                    <span class="badge bg-secondary">{{ auth()->user()->uname }}</span>
                @else
                    <span class="badge bg-secondary">Tamu</span>
                @endauth
                <span>{{ $total_observations }} observasi</span>

                </div>
                <div class="species-detail">
                <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Spesies Image">
                <div class="map" id="map"></div>
                </div>
                <div class="species-thumbnail">
                    <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Thumbnail" class="suggestion-photo" style="width: 50px; height: 50px; margin-right: 3px;">
                    <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Thumbnail" class="suggestion-photo" style="width: 50px; height: 50px; margin-right: 3px;">
                    <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Thumbnail" class="suggestion-photo" style="width: 50px; height: 50px;">
                </div>
                <div class="location-info">
                    <p id="locationName"><i class="fas fa-map-marker-alt" style="color: red;"></i> Memuat lokasi...</p>
                </div>

            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var latitude = {{ $species->latitude }};
                var longitude = {{ $species->longitude }};
                var locationInfo = document.getElementById('locationName');

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            locationInfo.innerHTML = `<i class="fas fa-map-marker-alt" style="color: red;"></i> ${data.display_name}`;
                        } else {
                            locationInfo.innerHTML = `<i class="fas fa-map-marker-alt" style="color: red;"></i> Lokasi tidak ditemukan`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching location:', error);
                        locationInfo.innerHTML = `<i class="fas fa-map-marker-alt" style="color: red;"></i> Lokasi tidak ditemukan`;
                    });
            });
        </script>
        <div id="suggestionDisplay">
            <strong>Usulan Nama oleh {{ $observerUname ?? 'Observer' }}:</strong>
            <span>{{ $species->nameLat ?? 'Tidak ada usulan' }}</span>
            <small>({{ $species->nameId ?? ''}})</small>
            </span>
            <p>{{ $species->notes ?? 'Deskripsi tidak tersedia' }}</p>
        </div>
        @guest
        <p>Silakan <a href="{{ route('login') }}">login</a> untuk memberikan penilaian dan saran.</p>
    @else
    <div class="identification mb-4">
        <h3>Apakah identifikasi di atas sudah benar?</h3>
        <form id="identificationForm">
            @csrf
            <input type="hidden" name="checklist_id" value="{{ $species->checklist_id }}">
            <input type="hidden" name="identification" value="1">
            <button type="submit" class="btn-success">Setuju</button>
        </form>
        <form id="cancelIdentificationForm" style="display: none;">
            @csrf
            <input type="hidden" name="checklist_id" value="{{ $species->checklist_id }}">
            <input type="hidden" name="identification" value="0">
            <button type="submit" class="btn-danger">Batal</button>
        </form>
        <div id="identificationSuccess" class="mt-2" style="display: none;">Identifikasi berhasil disimpan.</div>
        <div id="cancelIdentificationSuccess" class="mt-2" style="display: none;">Persetujuan dibatalkan.</div>
    </div>

    <div id="approvalDisplay">
        <p><strong>Disetujui oleh:</strong></p>
        <div id="approvedUsers">
            @foreach($approved_users as $index => $user)
                @if($index < 5)
                    <span>
                        <img src="{{ $user->profile_picture ?? asset('storage/icon/user.png') }}" alt="Profile Picture" width="30" class="rounded-circle">
                        <a href="#" onclick="showUserPopup({{ $user->id }})">{{ $user->uname }}</a>
                    </span>
                @endif
            @endforeach
            @if(count($approved_users) > 5)
                <span id="moreUsers" style="cursor: pointer; color: blue;">dan {{ count($approved_users) - 5 }} lainnya</span>
            @endif
        </div>
        <div id="allApprovedUsers" style="display: none;">
            @foreach($approved_users as $user)
                <span>
                    <img src="{{ $user->profile_picture ?? asset('storage/icon/default.png') }}" alt="Profile Picture" width="30" class="rounded-circle">
                    <a href="#" onclick="showUserPopup({{ $user->id }})">{{ $user->uname }}</a>
                </span>
            @endforeach
        </div>
    </div>
    @foreach($approved_users as $user)
    <div id="userPopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid #ccc; z-index: 1000;">
        <div class="popup-content">
            <a href="{{ route('profile.public', ['uname' => $user->uname]) }}">Lihat Profil</a>
            <button onclick="toggleFollow({{ $user->id }})" id="followButton">Follow/Unfollow</button>
            <button onclick="reportUser({{ $user->id }})">Laporkan User</button>
            <button onclick="closePopup()">Tutup</button>
        </div>
    </div>
    @endforeach
    <script>
        function showUserPopup(userId) {
            document.getElementById('userPopup').style.display = 'block';
            // Fetch follow status and update button text
            fetch(`/user/follow-status/${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('followButton').innerText = data.isFollowing ? 'Unfollow' : 'Follow';
                });
        }

        function closePopup() {
            document.getElementById('userPopup').style.display = 'none';
        }

        function toggleFollow(userId) {
            fetch(`/user/follow`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Follow status updated');
                    closePopup();
                }
            });
        }

        function reportUser(userId) {
            fetch(`/user/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User reported');
                    closePopup();
                }
            });
        }
    </script>
            <div id="ratingDisplay">
            <div class="rating-stars">
                @for ($i = 0; $i < 5; $i++)
                    <span>{{ $i < $average_rating ? '★' : '☆' }}</span>
                @endfor
                <span>Dinilai oleh {{ $rating_count }} pengguna</span>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var moreUsers = document.getElementById('moreUsers');
                var allApprovedUsers = document.getElementById('allApprovedUsers');

                if (moreUsers) {
                    moreUsers.addEventListener('click', function() {
                        if (allApprovedUsers.style.display === 'none') {
                            allApprovedUsers.style.display = 'block';
                            moreUsers.style.display = 'none';
                        }
                    });
                }
            });
        </script>
        <form id="ratingForm">
            @csrf
            <input type="hidden" name="checklist_id" value="{{ $species->checklist_id }}">
            <label for="rating">Nilai:</label>
            <div class="rating-stars" id="starRating">
                @for ($i = 1; $i <= 5; $i++)
                    <span data-value="{{ $i }}" class="star">&#9733;</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="rating" value="0">
            <button type="submit" class="btn-warning mt-0">Kirim Penilaian</button>
        </form>
        <div id="ratingSuccess" class="mt-2" style="display: none;">Penilaian berhasil disimpan.</div>

        <style>
            .rating-stars .star {
                font-size: 2rem;
                color: #ccc;
                cursor: pointer;
            }
            .rating-stars .star.selected,
            .rating-stars .star.hover {
                color: gold;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stars = document.querySelectorAll('#starRating .star');
                const ratingInput = document.getElementById('rating');

                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        ratingInput.value = value;
                        stars.forEach((s, index) => {
                            s.classList.toggle('selected', index < value);
                        });
                    });

                    star.addEventListener('mouseover', function() {
                        const value = this.getAttribute('data-value');
                        stars.forEach((s, index) => {
                            s.classList.toggle('hover', index < value);
                        });
                    });

                    star.addEventListener('mouseout', function() {
                        stars.forEach(s => s.classList.remove('hover'));
                    });
                });
            });
        </script>        <div id="ratingSuccess" class="mt-2" style="display: none;">Penilaian berhasil disimpan.</div>

<div id="suggestionsList" class="suggestions-list mt-4">
    @foreach($suggestions as $suggestion)
        @if($suggestion->fauna_id == $species->fauna_id)
            <div class="suggestion mb-3">
                <div class="user-info">
                    <img src="{{ $suggestion->user->profile_picture ?? 'default.jpg' }}" alt="Profile Picture" class="profile-picture">
                    <span class="{{ $suggestion->is_cancelled ? 'text-muted' : '' }}">
                        @if($suggestion->is_cancelled)
                            <s>{{ $suggestion->suggested_name }}</s>
                        @else
                            {{ $suggestion->suggested_name }}
                        @endif
                    </span>
                </div>
                <p>{!! $suggestion->description !!}</p>
                <span class="badge bg-secondary">
                    {{ $suggestion->user_id == Auth::id() ? 'Anda' : $suggestion->user->uname }}
                </span>
                <small>{{ $suggestion->created_at->format('d F Y') }}</small>
                @if($suggestion->user_id == Auth::id())
                    @if($suggestion->is_cancelled)
                        <small>Usulan telah ditarik</small>
                    @else
                        <button class="btn btn-link" onclick="cancelSuggestion({{ $suggestion->id }})">Tarik Usulan Saya</button>
                    @endif
                    <button class="btn btn-link" onclick="editSuggestion({{ $suggestion->id }})">Edit Usulan</button>
                    <button class="btn btn-link" onclick="deleteSuggestion({{ $suggestion->id }})">Hapus</button>
                @endif
            </div>
        @endif
    @endforeach
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel">Edit Usulan</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="editForm">
                    @csrf
                    <input type="hidden" id="editSuggestionId" name="suggestion_id">
                    <div class="form-group">
                      <label for="editSuggestedName">Nama Usulan</label>
                      <input type="text" id="editSuggestedName" name="suggested_name" class="form-control">
                    </div>
                    <div class="form-group">
                      <label for="editDescription">Deskripsi</label>
                      <textarea id="editDescription" name="description" class="form-control" rows="5"></textarea>
                      <script>
$(document).ready(function() {
    $('#editModal').on('shown.bs.modal', function () {
        if (!CKEDITOR.instances['editDescription']) {
            CKEDITOR.replace('editDescription');
        }
    });

    $('#editSuggestedName').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/fauna-suggestions',
                dataType: 'json',
                data: {
                    query: request.term
                },
                success: function(data) {
                    response(data.map(item => ({
                        label: `${item.nameId} (${item.nameLat})`,
                        value: item.nameLat
                    })));
                }
            });
        },
        minLength: 2
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        let suggestionId = $('#editSuggestionId').val();
        $.ajax({
            url: `/suggestions/${suggestionId}/update`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Usulan berhasil diperbarui.');
                location.reload();
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });
});

function editSuggestion(suggestionId) {
    $.ajax({
        url: `/suggestions/${suggestionId}/edit`,
        method: 'GET',
        success: function(data) {
            $('#editSuggestionId').val(data.id);
            $('#editSuggestedName').val(data.suggested_name);
            CKEDITOR.instances['editDescription'].setData(data.description);
            $('#editModal').modal('show');
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}
                    </script>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
                    <script>
function cancelSuggestion(suggestionId) {
    $.ajax({
        url: `/suggestions/${suggestionId}/cancel`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alert('Usulan berhasil ditarik.');
            location.reload();
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

function undoCancelSuggestion(suggestionId) {
    $.ajax({
        url: `/suggestions/${suggestionId}/undo-cancel`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alert('Pembatalan tarik usulan berhasil.');
            location.reload();
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

function editSuggestion(suggestionId) {
    // Ambil data usulan dari server atau gunakan data yang sudah ada
    $.ajax({
        url: `/suggestions/${suggestionId}/edit`,
        method: 'GET',
        success: function(data) {
            $('#editSuggestionId').val(data.id);
            $('#editSuggestedName').val(data.suggested_name);
            $('#editDescription').val(data.description);
            $('#editModal').modal('show');
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

function deleteSuggestion(suggestionId) {
    if (confirm('Apakah Anda yakin ingin menghapus usulan ini?')) {
        $.ajax({
            url: `/suggestions/${suggestionId}/delete`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Usulan berhasil dihapus.');
                location.reload();
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    }
}
            </script>
        </div>
        <div class="suggestion mt-5 mb-4">
            <h3>Usulkan Nama Spesies</h3>
            <form id="suggestionForm" method="POST" action="{{ route('suggestions.store') }}">
                @csrf
                <input type="hidden" name="checklist_id" value="{{ $species->checklist_id }}">
                <input type="hidden" name="fauna_id" value="{{ $fauna_id }}">
                <input type="text" id="suggestedName" name="suggested_name" placeholder="Usul nama" class="form-control w-25 mb-2">
                <div id="saran" class="saran-list">
                    <!-- Saran akan ditampilkan di sini -->
                </div>


                <div class="form-group">
                    <label for="description"></label>
                    <textarea id="description" name="description" class="form-control" rows="5"></textarea>

                </div>

                <script>
                            $(document).ready(function() {
    CKEDITOR.replace('description');
    document.getElementById('suggestionForm').addEventListener('submit', function() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
});
                            });
                            </script>

                <button type="submit" class="btn btn-secondary mt-2">Usulkan</button>
            </form>
                        <script>
                document.addEventListener('click', function(event) {
    const suggestions = document.getElementById('saran');
    const input = document.getElementById('suggestedName');

    if (!suggestions.contains(event.target) && event.target !== input) {
        suggestions.innerHTML = '';
    }
});
document.getElementById('suggestedName').addEventListener('input', function() {
    let query = this.value;
    if (query.length > 2) {
        fetch(`/fauna-suggestions?query=${query}`)
            .then(response => response.json())
            .then(data => {
                let suggestions = document.getElementById('saran');
                suggestions.innerHTML = '';
                data.forEach(fauna => {
                    let suggestionItem = document.createElement('li');
                    suggestionItem.classList.add('suggestion-item');
                    suggestionItem.textContent = `${fauna.nameId} (${fauna.nameLat})`;
                    suggestionItem.addEventListener('click', function() {
                        document.getElementById('suggestedName').value = fauna.nameLat;
                        suggestions.innerHTML = '';
                    });
                    suggestions.appendChild(suggestionItem);
                });
            });
    } else {
        document.getElementById('saran').innerHTML = '';
    }
});
            </script>
            <div id="suggestionSuccess" class="mt-2" style="display: none;">Saran nama berhasil disimpan.</div>
        </div>
        @endguest


    <script>
        var map = L.map('map').setView([{{ $species->latitude }}, {{ $species->longitude }}], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var locations = [
            { latitude: {{ $species->latitude }}, longitude: {{ $species->longitude }} }
        ];

        var gridSizes = [0.2, 0.1, 0.05, 0.02];
        var grids = [{}, {}, {}, {}];

        function getColor(count) {
            return count > 5 ? 'red' :
                   count > 2 ? 'orange' :
                   'orange';
        }

        locations.forEach(function(location) {
            gridSizes.forEach(function(gridSize, index) {
                let lat = Math.floor(location.latitude / gridSize) * gridSize;
                let lng = Math.floor(location.longitude / gridSize) * gridSize;
                let key = lat + ',' + lng;

                if (!grids[index][key]) {
                    grids[index][key] = 0;
                }
                grids[index][key]++;
            });
        });

        function addGrid(level) {
            for (var key in grids[level]) {
                var parts = key.split(',');
                var lat = parseFloat(parts[0]);
                var lng = parseFloat(parts[1]);
                var count = grids[level][key];

                var bounds = [
                    [lat, lng],
                    [lat + gridSizes[level], lng + gridSizes[level]]
                ];

                L.rectangle(bounds, {
                    color: getColor(count),
                    fillColor: getColor(count),
                    fillOpacity: 0.5,
                    weight: 1
                }).addTo(map).bindPopup(`Count: ${count}`);
            }
        }

        map.on('zoomend', function() {
            var zoomLevel = map.getZoom();
            clearGrids();
            if (zoomLevel > 11) {
                addGrid(3);
            } else if (zoomLevel >= 10) {
                addGrid(2);
            } else if (zoomLevel >= 8) {
                addGrid(1);
            } else {
                addGrid(0);
            }
        });

        function clearGrids() {
            map.eachLayer(function(layer) {
                if (layer instanceof L.Rectangle) {
                    map.removeLayer(layer);
                }
            });
        }

        // Inisialisasi grid terbesar
        addGrid(0);

        $(document).ready(function() {
            $('#ratingForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('rate') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#ratingSuccess').show().delay(3000).fadeOut();
                        updateRatingDisplay(response.average_rating, response.rating_count);
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });

            $('#identificationForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('identify') }}",
                        method: "POST",
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#identificationSuccess').show().delay(3000).fadeOut();
                            $('#identificationForm').hide();
                            $('#cancelIdentificationForm').show();
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    });
                });

                $('#cancelIdentificationForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('cancelIdentify') }}", // Pastikan rute ini ada di backend
                        method: "POST",
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#cancelIdentificationSuccess').show().delay(3000).fadeOut();
                            $('#cancelIdentificationForm').hide();
                            $('#identificationForm').show();
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    });
                });
            $('#suggestionForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('suggest') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#suggestionSuccess').show().delay(3000).fadeOut();
                        updateSuggestionDisplay(response.suggested_name, response.description);
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });

            function updateRatingDisplay(averageRating, ratingCount) {
                let stars = '';
                for (let i = 0; i < 5; i++) {
                    stars += i < averageRating ? '★' : '☆';
                }
                $('#ratingDisplay').html(`
                    <div class="rating-stars">
                        <span>${stars}</span>
                        <span>Dinilai oleh ${ratingCount} pengguna</span>
                    </div>
                `);
            }

            function updateSuggestionDisplay(suggestedName, description) {
                $('#suggestionDisplay').html(`
                    <p><strong>Usulan Nama oleh Observer:</strong> ${suggestedName}</p>
                    <p>${description}</p>
                `);
            }
        });
     </script>

    </body>
</html>
