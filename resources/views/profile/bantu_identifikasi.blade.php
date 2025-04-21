<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <title>Bantu Identifikasi</title>
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

        .upload-section .form-group {
            margin-bottom: 15px;
        }

        .upload-section .form-group label {
            font-weight: bold;
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

        .media-item img,
        .media-item audio {
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

        .observation-card {
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #ffffff;
        }

        .observation-card img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .observation-card .details {
            margin-top: 10px;
        }

        .observation-card .details p {
            margin: 0;
        }

        .observation-card .actions {
            margin-top: 10px;
            text-align: right;
        }

        .observation-card .actions .btn {
            background-color: #679995;
            color: #ffffff;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            text-decoration: none;
            color: #679995;
        }

        .pagination a.active {
            background-color: #679995;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
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
                                <a class="nav-link" href="{{ route('profile.bantu_identifikasi') }}">
                                    Bantu Ident
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="content">
                    <h5>Bantu Identifikasi</h5>
                    <div class="row">
                        @foreach($observations as $observation)
                            <div class="col-md-4">
                                <div class="observation-card">
                                    <img src="{{ asset('storage/' . $observation->media[0]) }}" alt="Observation Image">
                                    <div class="details">
                                        <p><strong>{{ $observation->scientific_name }}</strong></p>
                                        <p>{{ $observation->location }}</p>
                                        <p>{{ $observation->date }}</p>
                                    </div>
                                    <div class="actions">
                                        <a href="{{ route('profile.detail_observasi', $observation->id) }}" class="btn btn-outline-secondary">Detail</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pagination">
                        {{ $observations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
