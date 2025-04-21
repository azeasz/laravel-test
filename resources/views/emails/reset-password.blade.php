<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Talinara</title>
</head>
<body>
    <h2>Reset Password Talinara(Fobi)</h2>

    <p>Halo {{ $user->fname }},</p>

    <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>

    <p>Silakan klik tombol di bawah ini untuk mereset password Anda:</p>

    <a href="{{ $resetUrl }}"
       style="background-color: #4CAF50;
              color: white;
              padding: 14px 20px;
              margin: 8px 0;
              border: none;
              cursor: pointer;
              text-decoration: none;
              display: inline-block;">
        Reset Password
    </a>

    <p>Atau copy paste link berikut ke browser Anda:</p>
    <p>{{ $resetUrl }}</p>

    <p>Link ini akan kadaluarsa dalam 24 jam.</p>

    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>

    <p>Terima kasih,<br>Talinara</p>
</body>
</html>
