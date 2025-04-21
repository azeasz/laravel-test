<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str; // Tambahkan ini untuk menggunakan Str::random
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Models\AkaUser;
use App\Models\KupnesUser;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function showHome()
    {
        return view('/admin/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('login');
    }

    public function register(Request $request)
    {
        Log::debug('Register request data:', $request->all());

        $validatedData = $request->validate([
            'fname' => 'required|max:20',
            'lname' => 'required|max:20',
            'email' => 'required|email|max:50|unique:users',
            'uname' => 'required|max:50|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required|max:14',
            'organization' => 'required|max:50',
        ]);

        Log::info('Validated Data:', $validatedData); // Tambahkan ini untuk logging

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['level'] = 1; // Contoh dengan level 1
        $validatedData['ip_addr'] = $request->ip(); // Menambahkan IP address pengguna
        $validatedData['is_approved'] = 0; // Set is_approved menjadi 0

        $user = User::create($validatedData);

        Log::info('User Created:', $user->toArray()); // Tambahkan ini untuk logging

        // Generate token verifikasi
        $verificationToken = Str::random(60);
        $user->email_verification_token = $verificationToken;
        $user->save();

        // Kirim email verifikasi
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationToken));

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect('/verify-email')->with('error', 'Token verifikasi tidak valid.');
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->is_approved = 1; // Set is_approved menjadi 1 setelah verifikasi
        $user->save();

        return redirect('/verify-email')->with('success', 'Email Anda telah diverifikasi.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->is_approved) {
            return back()->withErrors([
                'email' => 'Email Anda belum diverifikasi. Silakan cek email Anda untuk verifikasi.',
            ]);
        }

        // Cek di database aka
        $akaUser = AkaUser::where('email', $credentials['email'])->first();
        if ($akaUser && Hash::check($credentials['password'], $akaUser->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        // Cek di database kupnes
        $kupnesUser = KupnesUser::where('email', $credentials['email'])->first();
        if ($kupnesUser && Hash::check($credentials['password'], $kupnesUser->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ]);
    }
    public function showLinkRequestForm()
    {
        return view('email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed', // Perbaiki aturan validasi di sini
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
