<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DummyAuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected DummyAuthService $auth;

    public function __construct(DummyAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = $this->auth->attempt($request->username, $request->password);

        if (!$user) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput();
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    public function logout()
    {
        $this->auth->logout();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
