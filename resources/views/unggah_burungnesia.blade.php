<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Burungnesia</title>
</head>
<body>
    <h1>Unggah Observasi Burungnesia</h1>
    <form action="{{ route('fobi.upload.store_burungnesia') }}" method="POST">
        @csrf
        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" required><br>

        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" required><br>

        <label for="observer">Observer:</label>
        <input type="text" name="observer" required><br>

        <label for="additional_note">Additional Note:</label>
        <textarea name="additional_note"></textarea><br>

        <label for="tgl_pengamatan">Tanggal Pengamatan:</label>
        <input type="date" name="tgl_pengamatan" required><br>

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" required><br>

        <label for="end_time">End Time:</label>
        <input type="time" name="end_time" required><br>

        <label for="tujuan_pengamatan">Tujuan Pengamatan:</label>
        <input type="text" name="tujuan_pengamatan" required><br>

        <button type="submit">Unggah</button>
    </form>
</body>
</html>
