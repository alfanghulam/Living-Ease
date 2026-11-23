<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Concerns\ThrottlesLogins;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    use ThrottlesLogins;

    public function showRegister() { return view('auth.register'); }
    public function showLogin() { return view('auth.login'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Registrasi berhasil! Selamat datang, '.$user->name.'!');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->loginThrottleKey($request, 'login');

        if ($message = $this->loginLockMessage($key)) {
            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        if (Auth::attempt($data)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Login berhasil! Selamat datang kembali, '.Auth::user()->name.'!');
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}