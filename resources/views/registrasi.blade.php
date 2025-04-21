<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <link rel="icon" href="{{ asset('storage/logo/icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Formulir Registrasi
                    </div>
                    <div class="card-body">
                        <form action="{{ route('register.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="fname" class="form-label">Nama Depan</label>
                                <input type="text" class="form-control" id="fname" name="fname" placeholder="Nama Depan" required>
                            </div>
                            <div class="mb-3">
                                <label for="lname" class="form-label">Nama Belakang</label>
                                <input type="text" class="form-control" id="lname" name="lname" placeholder="Nama Belakang" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <label for="uname" class="form-label">Username</label>
                                <input type="text" class="form-control" id="uname" name="uname" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon" required>
                            </div>
                            <div class="mb-3">
                                <label for="organization" class="form-label">Organisasi</label>
                                <input type="text" class="form-control" id="organization" name="organization" placeholder="Organisasi" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Daftar</button>
                        </form>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
