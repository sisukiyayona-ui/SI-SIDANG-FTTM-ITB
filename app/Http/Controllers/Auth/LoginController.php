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
            $existing = \App\Models\TUser::where('USERNAME', $request->username)->first();
            if ($existing && ($existing->STATUS_AKTIF ?? $existing->status_aktif) !== 'AKTIF') {
                return back()->withErrors([
                    'username' => 'Akun ini nonaktif. Hubungi admin.',
                ])->withInput();
            }
            if ($existing && !in_array($existing->STATUS_APPROVE ?? $existing->status_approve, ['t', 'y'], true)) {
                return back()->withErrors([
                    'username' => 'Akun belum disetujui admin.',
                ])->withInput();
            }

            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput();
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
    }

    public function redirectToSSO()
    {
        $ssoUrl = config('sso.url');
        $callback = route('sso.callback');
        return redirect($ssoUrl . '?callback=' . urlencode($callback));
    }

    public function handleSSOCallback(Request $request)
    {
        // Placeholder: handle SSO callback from ITB SSO provider
        // This will be implemented based on actual SSO protocol (CAS/OAuth/SAML)
        $username = $request->input('username');

        if (!$username) {
            return redirect()->route('login')->withErrors(['username' => 'SSO login gagal.']);
        }

        $user = $this->auth->attempt($username, null, true);

        if (!$user) {
            return redirect()->route('login')->withErrors(['username' => 'Akun SSO tidak ditemukan.']);
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
    }

    public function logout()
    {
        $this->auth->logout();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
