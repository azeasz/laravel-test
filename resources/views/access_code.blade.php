<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Code</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <img src="{{ asset('storage/logo/icon.png') }}" alt="Logo" class="img-fluid" style="max-width: 150px;">
        </div>
        @if(Auth::check() && Auth::user()->is_approved)
            <form method="POST" action="{{ url('/access-code') }}" class="form-inline justify-content-center">
                @csrf
                <div class="form-group mx-sm-3 mb-2">
                    <label for="access_code" class="sr-only">Masukkan Kode Akses:</label>
                    <input type="text" id="access_code" name="access_code" class="form-control" placeholder="Masukkan Kode Akses" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Submit</button>
            </form>
        @else
            <form method="POST" action="{{ url('/access-code') }}" class="form-inline justify-content-center">
                @csrf
                <div class="form-group mx-sm-3 mb-2">
                    <label for="access_code" class="sr-only">Masukkan Kode Akses:</label>
                    <input type="text" id="access_code" name="access_code" class="form-control" placeholder="Masukkan Kode Akses" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Submit</button>
            </form>
            <div class="alert alert-warning text-center mt-3">
                Masukan Kode Akses Untuk Mengakses Website Ini, hubungi admin untuk kode akses.
            </div>
        @endif
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
