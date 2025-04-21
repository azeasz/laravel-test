<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Identifikasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .detail-section .image img {
            width: 100%;
            height: auto;
        }
        .detail-section .info {
            width: 50%;
            padding: 20px;
        }
        #map {
            width: 60%;
            height: 400px;
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
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="logo.png" alt="Logo">
            <nav>
                <a href="#">Jelajah</a>
                <a href="#">Eksplorasi Saya</a>
                <a href="#">Bantu Ident</a>
                <a href="#">Komunitas</a>
            </nav>
            <button>Observasi Baru</button>
        </div>
        <div class="detail-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="image">
                <img src="{{ asset('storage/icon/blt.jpeg') }}" alt="Gambar">
                    </div>
                <div id="map"></div>
                <p>Nama ilmiah</p>
                <p>Biru-laut ekor-blorok</p>
                <p>Lokasi</p>
                <p>Tanggal</p>
            </div>
            <div class="info">
                <div class="scrollable">
                    <div class="comment">
                        <img src="user1.png" alt="User 1" width="50">
                        <div>
                            <p>Nama usulan spesies A</p>
                            <p>Terlihat jelas ekornya berwarna belang gelap-terang.</p>
                        </div>
                        <button>Setuju</button>
                    </div>
                    <div class="comment">
                        <img src="user2.png" alt="User 2" width="50">
                        <div>
                            <p>Nama usulan spesies B</p>
                            <p>Seperti mentog</p>
                        </div>
                        <button>Setuju</button>
                    </div>
                </div>
                <p>Usul nama</p>
                <input type="text" placeholder="Usul nama">
                <input type="checkbox"> Centang jika anda tidak ingin usul nama
                <textarea placeholder="Beri catatan atau komentar"></textarea>
                <button>Lampirkan foto</button>
            </div>
        </div>
    </div>
    <script>
        var map = L.map('map').setView([-6.200000, 106.816666], 13); // Koordinat Jakarta sebagai contoh

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([-6.200000, 106.816666]).addTo(map)
            .bindPopup('Lokasi Observasi')
            .openPopup();
    </script>
</body>
</html>
