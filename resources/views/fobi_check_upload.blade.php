<!DOCTYPE html>
<html>
<head>
    <title>Check Upload Status</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Check Upload Status</h1>

        <h2>Database Utama</h2>
        <ul>
            @foreach($mainData as $data)
                <li>ID: {{ $data->id }}, Latitude: {{ $data->latitude }}, Longitude: {{ $data->longitude }}</li>
            @endforeach
        </ul>

        <h2>Database Kedua (Burungnesia)</h2>
        <ul>
            @foreach($secondData as $data)
                <li>ID: {{ $data->id }}, Latitude: {{ $data->latitude }}, Longitude: {{ $data->longitude }}</li>
            @endforeach
        </ul>

        <h2>Database Ketiga (Kupunesia)</h2>
        <ul>
            @foreach($thirdData as $data)
                <li>ID: {{ $data->id }}, Latitude: {{ $data->latitude }}, Longitude: {{ $data->longitude }}</li>
            @endforeach
        </ul>
    </div>
</body>
</html>
