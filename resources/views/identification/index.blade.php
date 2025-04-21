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

        .observation-card {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .observation-card img {
            max-width: 100%;
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

        .filter-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-bar .filter-group {
            display: flex;
            align-items: center;
        }

        .filter-bar .filter-group label {
            margin-right: 10px;
        }

        .filter-bar .filter-group select,
        .filter-bar .filter-group input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h5>Bantu Identifikasi</h5>
            <div class="filter-bar">
                <div class="filter-group">
                    <label for="species">Spesies:</label>
                    <select id="species" class="form-control">
                        <option value="">Semua</option>
                        <!-- Tambahkan opsi spesies lainnya di sini -->
                    </select>
                </div>
                <div class="filter-group">
                    <label for="location">Lokasi:</label>
                    <input type="text" id="location" class="form-control" placeholder="Lokasi">
                </div>
                <div class="filter-group">
                    <label for="quality">Kualitas observasi:</label>
                    <select id="quality" class="form-control">
                        <option value="id_kurang">ID Kurang</option>
                        <option value="bantu_ident">Bantu Ident</option>
                    </select>
                </div>
                <button class="btn btn-primary">Filter</button>
            </div>
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
                                <a href="{{ route('identification.show', $observation->id) }}" class="btn btn-outline-secondary">Detail</a>
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
</body>
</html>
