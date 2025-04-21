<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Diskusi Identifikasi</title>
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

        .dropdown-menu, .show a{
            margin: 0;
            position: relative;
            bottom: 2px;
        }



        .content {
            padding: 20px;
            background: #f0f0f0;
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

        .discussion-item {
            display: flex;
            margin-bottom: 20px;
            padding: 10px;
            background: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .discussion-item img {
            width: 100px;
            height: 100px;
            background-color: #dcdcdc;
            display: block;
            margin-right: 20px;
        }

        .discussion-item .details {
            flex: 1;
        }

        .discussion-item .details h6 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .discussion-item .details p {
            margin: 5px 0;
            font-size: 14px;
            color: #666666;
        }

        .discussion-item .details .meta {
            font-size: 12px;
            color: #999999;
        }

        .discussion-item .details .meta i {
            margin-right: 5px;
        }

        .discussion-item .actions {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .discussion-item .actions .btn {
            font-size: 12px;
            padding: 5px 10px;
        }
        .observation-card {
    display: flex;
    margin-bottom: 20px;
    padding: 10px;
    background: #ffffff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.observation-card img {
    width: 100px;
    height: 100px;
    background-color: #dcdcdc;
    display: block;
    margin-right: 20px;
}

.details {
    flex: 1;
}

.details p {
    margin: 5px 0;
    font-size: 14px;
    color: #666666;
}

.actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.comments {
    margin-top: 10px;
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 5px;
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
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.spesies_saya') }}">
                                    Spesies saya
                                </a>
                            </li>
                            <li class="nav-item active">
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
                    <h5>Diskusi Identifikasi</h5>
                    <p>Di sini anda bisa memantau proses identifikasi taksa oleh para observer dan identifier. Meskipun anda bukan ahli dalam taksa yang didiskusikan, komunitas fobi akan sangat senang mendengar pendapat atau pertanyaan anda.</p>


                    <div class="row">
                        @php
                        $displayedDiscussions = [];
                        @endphp
                        @foreach($paginatedDiscussions as $discussion)
                            @php
                            $key = $discussion->checklist_id . '-' . $discussion->fauna_id;
                            @endphp
                            @if(!in_array($key, $displayedDiscussions))
                                <div class="col-md-6">
                                    <div class="observation-card">
                                        <img src="{{ asset('storage/default-image.png') }}" alt="Observation Image">
                                        @if($discussion->suggestion->isNotEmpty())
                                        <div class="details">
                                            <p>{{ e($discussion->checklist_id) }}</p>
                                            <p>{{ e($discussion->fauna_id) }}</p>
                                            @php
                                            $firstSuggestion = $discussion->suggestion->firstWhere('fauna_id', $discussion->fauna_id);
                                            @endphp
                                            @if($firstSuggestion)
                                                <p><strong>{{ e($firstSuggestion->scientific_name ?? 'Unknown') }}</strong></p>
                                                <span>{{ e($firstSuggestion->uname ?? 'Unknown') }}</span>
                                                <p>Lokasi: {{ e($firstSuggestion->latitude ?? 'Unknown') }}, {{ e($firstSuggestion->longitude ?? 'Unknown') }}</p>
                                                <span>{{ e($discussion->created_at->format('d M Y')) }}</span>
                                            @endif

                                            <div class="actions">
                                                <button class="btn btn-outline-secondary" onclick="toggleComments({{ $discussion->id }})">
                                                    <i class="fa fa-comments"></i> {{ e($discussion->comments_count) }} Komentar
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="toggleIdentifications({{ $discussion->id }})">
                                                    <i class="fa fa-search"></i> {{ e($discussion->identifications_count) }} Identifikasi
                                                </button>
                                            </div>
                                            <div id="comments-{{ $discussion->id }}" class="comments" style="display: none;">
                                                <p>
                                                    @foreach($discussion->suggestion->unique('id') as $suggestion)
                                                        @if($suggestion->fauna_id == $discussion->fauna_id && $suggestion->user_id == auth()->id())
                                                            Anda
                                                        @else
                                                            {{ e($suggestion->user->uname) }}
                                                        @endif
                                                        {!! $suggestion->description !!}<br>
                                                    @endforeach
                                                </p>
                                            </div>
                                            <div id="identifications-{{ $discussion->id }}" class="identifications" style="display: none;">
                                                <div class="identifications-content">
                                                    @foreach($discussion->suggestion->unique('id') as $suggestion)
                                                        @if($suggestion->fauna_id == $discussion->fauna_id && $suggestion->user_id == auth()->id())
                                                            <p>
                                                                {{ e($suggestion->suggested_name) }} - Anda
                                                                <br>
                                                            </p>
                                                        @else
                                                            <p>
                                                                {{ e($suggestion->suggested_name) }} - {{ e($suggestion->user->uname) }}
                                                                <br>
                                                            </p>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $displayedDiscussions[] = $key;
                                @endphp
                            @endif
                        @endforeach
                    </div>

                    <script>
                        function toggleComments(discussionId) {
                            var commentsDiv = document.getElementById('comments-' + discussionId);
                            commentsDiv.style.display = commentsDiv.style.display === 'none' ? 'block' : 'none';
                        }

                        function toggleIdentifications(discussionId) {
                            var identificationsDiv = document.getElementById('identifications-' + discussionId);
                            identificationsDiv.style.display = identificationsDiv.style.display === 'none' ? 'block' : 'none';
                        }
                    </script>
                    <div class="pagination">
                            {{ $paginatedDiscussions->links('vendor.pagination.custom') }}
                    </div>
                </div>
                    {{-- <button id="load-more" class="btn btn-outline-secondary">Muat lebih</button> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loadMoreButton = document.getElementById('load-more');
            var page = 1;

            loadMoreButton.addEventListener('click', function() {
                page++;
                fetch('/profile/diskusi-identifikasi?page=' + page)
                    .then(response => response.json())
                    .then(data => {
                        data.discussions.forEach(discussion => {
                            var discussionItem = document.createElement('div');
                            discussionItem.classList.add('discussion-item');
                            discussionItem.innerHTML = `
                                <img src="/storage/default-image.png" alt="Discussion Image">
                                <div class="details">
                                    <h6>${discussion.scientific_name}</h6>
                                    <p>Lokasi: ${discussion.latitude}, ${discussion.longitude}</p>
                                    <p>Tanggal: ${discussion.date}</p>
                                </div>
                                <div class="actions">
                                    <a href="/observasi/${discussion.id}" class="btn btn-outline-secondary">Lihat observasi</a>
                                </div>
                            `;
                            document.querySelector('.row').appendChild(discussionItem);
                        });
                    });
            });
        });
    </script>
    </body>
</html>
