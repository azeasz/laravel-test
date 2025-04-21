<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{ asset('storage/icon/FOBi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-box {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
            scale: 0.8;
            text-align: center;
            justify-content: center;
        }
        .icon{
            text-align: center;
            font-size: 12px;
            scale: 0.5;
            top: 0;
        }
        .text-center{
            text-align: center;
        }
        .text-icon{
            text-align: center;
            height: 100px;
            translate: 0 -50%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="login-box">
            <div class="text-icon">
                <p class="icon"><img src="{{ asset('storage/icon/FOBi.png') }}" alt="Logo" class="img-fluid mb-3"></p>
            </div>
            <h5 class="text-center">Login <i class="fas fa-money-bill-alt    "></i></h5>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                {{-- <div class="mb-3">
                    hubungkan dengan burungnesia
                    hubungkan dengan kupunesia
                    keduanya
                </div> --}}
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat Saya</label>
                </div>
                <button type="submit" class="btn btn-success w-100" style="background-color: #679995; border: none;">Masuk</button>
            </form>
            <div class="mt-3 text-center">
                <a href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
            </div>
            <div class="mt-3 text-center">
                <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
