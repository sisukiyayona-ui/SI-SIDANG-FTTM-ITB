<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DummyAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    protected const MAX_ATTEMPTS = 5;
    protected const LOCK_MINUTES = 15;

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

        $usernameKey = 'login_attempts:' . strtolower(trim($request->username));

        $lockUntil = Cache::get($usernameKey . ':lock');
        if ($lockUntil) {
            $minutes = (int) ceil(($lockUntil - now()->getTimestamp()) / 60);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$minutes} menit.",
            ])->withInput();
        }

        $user = $this->auth->attempt($request->username, $request->password);

        if (!$user) {
            $attempts = (int) Cache::get($usernameKey . ':count', 0) + 1;
            Cache::put($usernameKey . ':count', $attempts, now()->addMinutes(15));
            if ($attempts >= self::MAX_ATTEMPTS) {
                $lock = now()->addMinutes(self::LOCK_MINUTES);
                Cache::put($usernameKey . ':lock', $lock->getTimestamp(), $lock);
                Cache::forget($usernameKey . ':count');
            }

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

        Cache::forget($usernameKey . ':count');
        Cache::forget($usernameKey . ':lock');

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
    }

    public function redirectToSSO()
    {
        if (!config('sso.enabled') || (string) config('sso.callback_token') === '') {
            return redirect()->route('login')->with('error', 'Login SSO belum dikonfigurasi.');
        }

        $ssoUrl = config('sso.url');
        $callback = route('sso.callback');
        return redirect($ssoUrl . '?callback=' . urlencode($callback));
    }

    public function handleSSOCallback(Request $request)
    {
        // Keamanan: callback SSO hanya boleh diterima bila membawa token rahasia
        // bersama (shared secret) yang juga diketahui server SSO. Tanpa token yang
        // valid, endpoint ini ditolak — mencegah siapapun "login as" user lain
        // cukup dengan mengetik /login/sso/callback?username=xxx.
        $expected = (string) config('sso.callback_token');
        $provided = (string) $request->input('token');

        if ($expected === '' || !hash_equals($expected, $provided)) {
            return redirect()->route('login')->withErrors(['username' => 'SSO login gagal.']);
        }

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
