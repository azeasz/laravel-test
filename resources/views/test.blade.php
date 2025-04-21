<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Level Spesies</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f0f0f0;
            border-bottom: 5px solid #679995;
        }

        .header .logo img {
            height: 65px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header .user-info i {
            font-size: 30px;
        }

        .gallery {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .gallery-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        }

        .gallery-header .stats {
            display: flex;
            gap: 20px;
        }

        .gallery-header .stats div {
            text-align: center;
        }

        .gallery-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .gallery-item {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fff;
            width: 200px;
            text-align: center;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
        }

        .similar-species, .map {
            margin-top: 20px;
            width: 100%;
        }

        .similar-species img, .map img {
            width: 100%;
            height: auto;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .popup img {
            width: 100%;
            height: auto;
        }

        .popup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .filter-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .filter-container input, .filter-container button {
            margin: 0 10px;
            padding: 10px;
            font-size: 16px;
        }

        .filter-container button {
            background-color: #ffc107;
            border: none;
            cursor: pointer;
        }

        .filter-container button:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="{{ asset('storage/icon/FOBI.png') }}" alt="Fobi Logo">
        </div>
        <div class="user-info">
            <span><i class="fas fa-user-circle"></i></span>
            @auth
                <span>{{ Auth::user()->uname }}</span>
            @else
                <span><a href="{{ route('login') }}">Login</a></span>
            @endauth
            <i class="fa fa-bell"></i>
            <i class="fa fa-envelope"></i>
            <span><strong>120</strong><br><small>Observasi</small></span>
        </div>
    </header>

    <div class="container">
        <div class="filter-container">
            <input type="text" placeholder="Spesies/genus/famili">
            <input type="text" placeholder="Lokasi">
            <button>Filter</button>
        </div>

        <div class="gallery">
            <div class="gallery-header">
                <h2>Limosa limosa</h2>
                <div class="stats">
                    <div>
                        <strong>1.023</strong>
                        <p>Observasi</p>
                    </div>
                    <div>
                        <strong>100</strong>
                        <p>Foto</p>
                    </div>
                    <div>
                        <strong>10</strong>
                        <p>Audio</p>
                    </div>
                    <div>
                        <strong>1</strong>
                        <p>Checklist Burnes</p>
                    </div>
                    <div>
                        <strong>34</strong>
                        <p>Checklist Kupnesia</p>
                    </div>
                </div>
            </div>

            <div class="gallery-content">
                <div class="gallery-item">
                    <img src="path/to/image1.jpg" alt="Observasi 1">
                    <p>Observasi 1</p>
                </div>
                <div class="gallery-item">
                    <img src="path/to/image2.jpg" alt="Observasi 2">
                    <p>Observasi 2</p>
                </div>
                <!-- Tambahkan item galeri lainnya di sini -->
            </div>

            <div class="similar-species">
                <h3>Spesies Mirip</h3>
                <div class="gallery-content">
                    <div class="gallery-item">
                        <img src="path/to/similar1.jpg" alt="Limosa harlequin">
                        <p>Limosa harlequin</p>
                    </div>
                    <div class="gallery-item">
                        <img src="path/to/similar2.jpg" alt="Limosa hutchinsii">
                        <p>Limosa hutchinsii</p>
                    </div>
                </div>
            </div>

            <div class="map">
                <h3>Peta</h3>
                <img src="path/to/map.jpg" alt="Peta">
            </div>
        </div>
    </div>

    <div class="popup" id="popup">
        <span class="close" onclick="closePopup()">&times;</span>
        <img src="path/to/large-image.jpg" alt="Large Image">
    </div>

    <script>
        function openPopup(imageSrc) {
            const popup = document.getElementById('popup');
            popup.querySelector('img').src = imageSrc;
            popup.style.display = 'block';
        }

        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('.gallery-item img');
            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    openPopup(this.src);
                });
            });
        });
    </script>
</body>
</html>
