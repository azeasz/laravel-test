<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FobiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Http;

class FobiUserController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'fname' => 'required|max:20',
            'lname' => 'required|max:20',
            'email' => 'required|email|max:50',
            'uname' => 'required|max:50|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required|max:14',
            'organization' => 'required|max:50',
            'burungnesia_email' => 'nullable|email',
            'kupunesia_email' => 'nullable|email',
        ]);

        $burungnesiaUserId = null;
        $kupunesiaUserId = null;

        if ($request->burungnesia_email) {
            $burungnesiaUser = DB::connection('second')->table('users')->where('email', $request->burungnesia_email)->first();
            if ($burungnesiaUser) {
                $burungnesiaUserId = $burungnesiaUser->id;
            }
        }

        if ($request->kupunesia_email) {
            $kupunesiaUser = DB::connection('third')->table('users')->where('email', $request->kupunesia_email)->first();
            if ($kupunesiaUser) {
                $kupunesiaUserId = $kupunesiaUser->id;
            }
        }

        $user = FobiUser::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'burungnesia_email' => $request->burungnesia_email,
            'kupunesia_email' => $request->kupunesia_email,
            'uname' => $request->uname,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'organization' => $request->organization,
            'ip_addr' => $request->ip(),
            'level' => 1,
            'is_approved' => 0,
            'burungnesia_user_id' => $burungnesiaUserId,
            'kupunesia_user_id' => $kupunesiaUserId,
            'email_verification_token' => Str::random(60),
            'burungnesia_email_verification_token' => $request->burungnesia_email ? Str::random(60) : null,
            'kupunesia_email_verification_token' => $request->kupunesia_email ? Str::random(60) : null,
        ]);

        Mail::to($user->email)->send(new VerifyEmail($user, 'email_verification_token'));

        if ($request->burungnesia_email) {
            Mail::to($request->burungnesia_email)->send(new VerifyEmail($user, 'burungnesia_email_verification_token'));
        }

        if ($request->kupunesia_email) {
            Mail::to($request->kupunesia_email)->send(new VerifyEmail($user, 'kupunesia_email_verification_token'));
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan cek semua email Anda untuk verifikasi.');
    }

    public function verifyEmail($token, $type)
    {
        $user = FobiUser::where($type, $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Token verifikasi tidak valid.');
        }

        if ($type === 'email_verification_token') {
            $user->email_verified_at = now();
            $user->email_verification_token = null;
        } elseif ($type === 'burungnesia_email_verification_token') {
            $user->burungnesia_email_verified_at = now();
            $user->burungnesia_email_verification_token = null;
        } elseif ($type === 'kupunesia_email_verification_token') {
            $user->kupunesia_email_verified_at = now();
            $user->kupunesia_email_verification_token = null;
        }

        // Cek apakah semua email yang relevan sudah diverifikasi
        if ($user->email_verified_at &&
            (!$user->burungnesia_email || $user->burungnesia_email_verified_at) &&
            (!$user->kupunesia_email || $user->kupunesia_email_verified_at)) {
            $user->is_approved = 1;
        }

        $user->save();

        return redirect('/login')->with('success', 'Email Anda telah diverifikasi. Silakan login.');
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
                'recaptcha_token' => 'required|string'
            ]);

            // Verifikasi reCAPTCHA
            $recaptchaSecret = config('services.recaptcha.secret_key');
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $request->recaptcha_token,
            ]);

            if (!$response->json()['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi keamanan gagal'
                ], 400);
            }

            $user = FobiUser::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            if (!$user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'error' => 'EMAIL_NOT_VERIFIED',
                    'message' => 'Email belum diverifikasi'
                ], 403);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'uname' => $user->uname,
                    'email' => $user->email,
                    'level' => $user->level,
                    'burungnesia_user_id' => $user->burungnesia_user_id,
                    'kupunesia_user_id' => $user->kupunesia_user_id,
                    'bio' => $user->bio,
                    'profile_picture' => $user->profile_picture
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Login error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
