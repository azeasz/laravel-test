<!DOCTYPE html>
<html>
<head>
    <style>
        .email-container {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1a4d2e;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1a4d2e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Verifikasi Email Talinara(Fobi)</h1>
        </div>

        <div class="content">
            <h2>Halo {{ $user->fname }},</h2>

            @if($tokenType === 'email_verification_token')
                <p>Terima kasih telah mendaftar di Talinara. Silakan klik tombol di bawah ini untuk memverifikasi email Anda:</p>
            @elseif($tokenType === 'burungnesia_email_verification_token')
                <p>Silakan verifikasi email Burungnesia Anda untuk mengintegrasikan data:</p>
            @elseif($tokenType === 'kupunesia_email_verification_token')
                <p>Silakan verifikasi email Kupunesia Anda untuk mengintegrasikan data:</p>
            @endif

            <center>
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
            </center>

            <p>Atau klik link berikut:</p>
            <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>

            <p>Jika Anda tidak merasa mendaftar di Talinara, Anda dapat mengabaikan email ini.</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Talinara</p>
        </div>
    </div>
</body>
</html>
