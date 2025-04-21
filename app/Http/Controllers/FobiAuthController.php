<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\FobiUser;
use App\Mail\VerifyEmail;

class FobiAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('fobi.login');
    }

    public function showRegisterForm()
    {
        return view('fobi.register');
    }

    public function register(Request $request)
    {
        Log::debug('Register request data:', $request->all());

        $validatedData = $request->validate([
            'fname' => 'required|max:20',
            'lname' => 'required|max:20',
            'email' => 'required|email|max:50|unique:fobi_users,email',
            'uname' => 'required|max:50|unique:fobi_users,uname',
            'password' => 'required|min:6',
            'phone' => 'required|max:14',
            'organization' => 'required|max:50',
            'link_burungnesia' => 'nullable|boolean',
        ]);

        Log::info('Validated Data:', $validatedData);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['level'] = 1;
        $validatedData['ip_addr'] = $request->ip();
        $validatedData['is_approved'] = 0;

        $user = FobiUser::create($validatedData);

        Log::info('User Created:', $user->toArray());

        $verificationToken = Str::random(60);
        $user->email_verification_token = $verificationToken;
        $user->save();

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationToken));

        return redirect('/fobi/login')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = FobiUser::where('email', $credentials['email'])->first();

        if ($user && !$user->is_approved) {
            return back()->withErrors([
                'email' => 'Email Anda belum diverifikasi. Silakan cek email Anda untuk verifikasi.',
            ]);
        }

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/fobi/home');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ]);
    }

    public function showHome()
    {
        return view('fobi.home');
    }
}
