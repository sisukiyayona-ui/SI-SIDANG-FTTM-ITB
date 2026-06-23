<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DummyAuthService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected DummyAuthService $auth;

    public function __construct(DummyAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'username' => 'required|min:3',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'akun_ina' => 'required',
        ]);

        $this->auth->register($request->only(['name', 'email', 'username', 'password', 'akun_ina']));

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan tunggu persetujuan admin.');
    }
}
