<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verifikasi Email</title>
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
</head>
<body>
    <h1>Verifikasi Email</h1>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @elseif (session('error'))
        <p>{{ session('error') }}</p>
    @endif
</body>
</html>
