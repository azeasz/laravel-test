<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist {{ ucfirst($source) }}</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header img {
            height: 40px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .map {
            width: 100%;
            height: 300px;
            background-color: #e9ecef;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="path/to/logo.png" alt="Logo">
        <div>
            <span>Jelajahi</span> |
            <span>Eksplorasi Saya</span> |
            <span>Bantu Ident</span> |
            <span>Komunitas</span>
        </div>
        <div>
            <span>Sikebo</span> |
            <span>120 Observasi</span>
        </div>
    </div>
    <div class="container">
        <h1>Checklist {{ ucfirst($source) }}</h1>
        <p>lokasi</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Spesies</th>
                    <th>Jumlah</th>
                    <th>Catatan Kawin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checklists as $checklist)
                <tr>
                    {{-- <td>
                        <img src="{{ $checklist->image }}" alt="Image" style="width: 50px; height: auto; margin-right: 10px;">
                        {{ $checklist->species }}
                    </td>
                    <td>{{ $checklist->jumlah }}</td>
                    <td>{{ $checklist->catatan_kawin }}</td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="map">
            <!-- Map placeholder -->
        </div>
    </div>
</body>
</html>
