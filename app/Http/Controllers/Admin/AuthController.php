<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Concerns\ThrottlesLogins;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    use ThrottlesLogins;

    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->loginThrottleKey($request, 'admin-login');

        if ($message = $this->loginLockMessage($key)) {
            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        if (Auth::attempt($request->only('email', 'password')) && Auth::user()->is_admin) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        Auth::logout();
        RateLimiter::hit($key, 60);

        return back()->withErrors(['email' => 'Email/password salah, atau akun ini bukan admin.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}   