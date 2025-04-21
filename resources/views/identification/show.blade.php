<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Detail Observasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>

    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
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

        .suggestion-card {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .suggestion-card .details {
            margin-top: 10px;
        }

        .suggestion-card .details p {
            margin: 0;
        }

        .suggestion-card .actions {
            margin-top: 10px;
            text-align: right;
        }

        .suggestion-card .actions .btn {
            background-color: #679995;
            color: #ffffff;
        }

        .scrollable {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dcdcdc;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                {{-- <div class="profile-sidebar">
                    <div>
                        <img src="{{ asset('storage/icon/user.png') }}" alt="User Image" width="100">
                        <strong>{{ $user->fname }} {{ $user->lname }}</strong>
                        <small>{{ $user->uname }}</small>
                    </div>

                    <div class="profile-usermenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile') }}">
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
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ route('identification.index') }}">
                                    Bantu Ident
                                </a>
                            </li>
                        </ul>
                    </div>
                </div> --}}
            </div>
            <div class="col-md-9">
                <div class="content">
                    <h5>Detail Observasi</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{ asset('storage/' . $observation->media[0]) }}" alt="Observation Image" class="img-fluid">
                        </div>
                        <div class="col-md-6">
                            <p><strong>Nama Ilmiah:</strong> {{ $observation->scientific_name }}</p>
                            <p><strong>Lokasi:</strong> {{ $observation->location }}</p>
                            <p><strong>Tanggal:</strong> {{ $observation->date }}</p>
                            <div class="scrollable">
                                @foreach($suggestions as $suggestion)
                                    <div class="suggestion-card">
                                        <div class="details">
                                            <p><strong>{{ $suggestion->user->uname }}</strong></p>
                                            <p>{{ $suggestion->name }}</p>
                                            <p>{{ $suggestion->description }}</p>
                                        </div>
                                        <div class="actions">
                                            <button class="btn btn-outline-secondary">Setuju</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form action="{{ route('identification.storeSuggestion', $observation->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Usul Nama</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="no_suggestion" id="no_suggestion" class="form-check-input">
                                    <label for="no_suggestion" class="form-check-label">Centang jika anda tidak ingin usul nama</label>
                                </div>
                                <button type="submit" class="btn">Usul Nama</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

