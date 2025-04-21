<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard')->with('success', 'Berhasil login');
        }

        return back()->withErrors(['email' => 'Email atau kata sandi salah']);
    }

    public function loginWithExternalApp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'external_app' => 'required|array',
            'external_email' => 'required|email',
            'external_password' => 'required',
        ]);

        $externalApps = $request->input('external_app');
        $externalEmail = $request->input('external_email');
        $externalPassword = $request->input('external_password');

        foreach ($externalApps as $externalApp) {
            switch ($externalApp) {
                case 'burungnesia':
                    $response = Http::post('https://api.burungnesia.com/login', [
                        'email' => $externalEmail,
                        'password' => $externalPassword,
                    ]);
                    break;
                case 'kupunesia':
                    $response = Http::post('https://api.kupunesia.com/login', [
                        'email' => $externalEmail,
                        'password' => $externalPassword,
                    ]);
                    break;
                default:
                    return back()->withErrors(['external_app' => 'Aplikasi tidak valid']);
            }

            if ($response->failed()) {
                return back()->withErrors(['external_app' => 'Login ke aplikasi ' . ucfirst($externalApp) . ' gagal']);
            }

            // Logika untuk menyimpan data dari aplikasi eksternal
            $externalData = $response->json();
            // Simpan data ke database atau session
            // ...
        }

        return redirect()->route('dashboard')->with('success', 'Berhasil login dengan aplikasi eksternal');
    }
}
