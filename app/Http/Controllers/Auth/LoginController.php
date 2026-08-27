<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TUser;
use App\Services\DummyAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected const MAX_ATTEMPTS = 5;
    protected const LOCK_MINUTES = 15;

    protected DummyAuthService $auth;

    public function __construct(DummyAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function showLoginForm(Request $request)
    {
        // Jika ada parameter dari Azure AD OAuth callback, handle langsung
        if ($request->has('code') && $request->has('state')) {
            return $this->handleAzureADCallback($request);
        }

        return view('auth.login');
    }

    // ──────────────────────────── Normal login ────────────────────────────

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

            return back()->withErrors([
                'username' => 'Username atau password yang Anda masukkan salah.',
            ])->withInput();
        }

        Cache::forget($usernameKey . ':count');
        Cache::forget($usernameKey . ':lock');

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
    }

    // ──────────────────────────── SSO ──────────────────────────────

    public function redirectToSSO()
    {
        if (!config('sso.enabled')) {
            return redirect()->route('login')->with('error', 'Login SSO belum dikonfigurasi.');
        }

        $provider = config('sso.provider');

        if ($provider === 'azure_ad') {
            return $this->redirectToAzureAD();
        }

        // Fallback: SSO ITB lama (shared-secret)
        return $this->redirectToLegacySSO();
    }

    public function handleSSOCallback(Request $request)
    {
        if (!config('sso.enabled')) {
            return redirect()->route('login')->with('error', 'SSO tidak aktif.');
        }

        $provider = config('sso.provider');

        if ($provider === 'azure_ad') {
            return $this->handleAzureADCallback($request);
        }

        return $this->handleLegacyCallback($request);
    }

    // ──────────────────────────── Azure AD ───────────────────────────────

    protected function redirectToAzureAD()
    {
        $tenantId = config('sso.azure.tenant_id');
        $clientId = config('sso.azure.client_id');
        $redirectUri = config('sso.azure.redirect_uri');

        if (!$tenantId || !$clientId) {
            return redirect()->route('login')->with('error', 'Azure AD belum dikonfigurasi.');
        }

        $state = Str::random(40);
        Cache::put('sso_state:' . $state, true, now()->addMinutes(10));

        $params = http_build_query([
            'client_id'     => $clientId,
            'response_type' => 'code',
            'redirect_uri'  => $redirectUri,
            'scope'         => 'openid profile email',
            'response_mode' => 'query',
            'state'         => $state,
        ]);

        $authorizeUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?{$params}";

        return redirect($authorizeUrl);
    }

    protected function handleAzureADCallback(Request $request)
    {
        // ── Validasi state ──
        $state = $request->input('state');
        if (!$state || !Cache::pull('sso_state:' . $state)) {
            return redirect()->route('login')->withErrors(['username' => 'SSO gagal: state tidak valid (kedaluwarsa atau palsu).']);
        }

        // ── Cek error dari Azure AD ──
        if ($request->input('error')) {
            $desc = $request->input('error_description', $request->input('error'));
            return redirect()->route('login')->withErrors(['username' => 'SSO dibatalkan: ' . $desc]);
        }

        // ── Ambil authorization code ──
        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('login')->withErrors(['username' => 'SSO gagal: tidak ada kode otorisasi.']);
        }

        // ── Tukar code → token ──
        $tokenResponse = Http::asForm()->post(
            "https://login.microsoftonline.com/" . config('sso.azure.tenant_id') . "/oauth2/v2.0/token",
            [
                'client_id'     => config('sso.azure.client_id'),
                'client_secret' => config('sso.azure.client_secret'),
                'code'          => $code,
                'redirect_uri'  => config('sso.azure.redirect_uri'),
                'grant_type'    => 'authorization_code',
            ]
        );

        if ($tokenResponse->failed()) {
            return redirect()->route('login')->withErrors(['username' => 'SSO gagal: token tidak valid.']);
        }

        $idToken = $tokenResponse->json('id_token');
        if (!$idToken) {
            return redirect()->route('login')->withErrors(['username' => 'SSO gagal: tidak ada ID token.']);
        }

        // ── Decode JWT payload ──
        $payload = $this->decodeJwtPayload($idToken);
        if (!$payload) {
            return redirect()->route('login')->withErrors(['username' => 'SSO gagal: token tidak bisa dibaca.']);
        }

        // ── Ambil info user dari Azure AD ──
        $azureEmail   = $payload['email'] ?? $payload['preferred_username'] ?? '';
        $azureName    = $payload['name'] ?? '';
        $azureUpn     = $payload['preferred_username'] ?? '';

        // ── Cocokkan dengan database via AKUN_INA ──
        $user = TUser::where('AKUN_INA', $azureEmail)
            ->orWhere('AKUN_INA', $azureUpn)
            ->first();

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'username' => "Akun SSO tidak ditemukan. Email tidak cocok dengan AKUN_INA di database.",
            ]);
        }

        if ($user->STATUS_AKTIF !== 'AKTIF') {
            return redirect()->route('login')->withErrors(['username' => 'Akun ini nonaktif. Hubungi admin.']);
        }

        if (!in_array($user->STATUS_APPROVE, ['t', 'y'], true)) {
            return redirect()->route('login')->withErrors(['username' => 'Akun belum disetujui admin.']);
        }

        // ── Login pakai data dari database ──
        $userData = $this->auth->attempt($user->USERNAME, null, true);
        if (!$userData) {
            return redirect()->route('login')->withErrors(['username' => 'Gagal login SSO.']);
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $userData['nama_lengkap'] . '!');
    }

    // ──────────────────────────── Legacy SSO (ITB) ──────────────────────────

    protected function redirectToLegacySSO()
    {
        $ssoUrl = config('sso.url');
        $callbackToken = config('sso.callback_token');

        if (empty($callbackToken)) {
            return redirect()->route('login')->with('error', 'SSO belum dikonfigurasi (callback_token kosong).');
        }

        $callback = route('sso.callback');
        return redirect($ssoUrl . '?callback=' . urlencode($callback));
    }

    protected function handleLegacyCallback(Request $request)
    {
        $expected  = (string) config('sso.callback_token');
        $provided  = (string) $request->input('token');

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

    // ──────────────────────────── Logout ───────────────────────────

    public function logout()
    {
        $this->auth->logout();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    // ──────────────────────────── Helpers ────────────────────────────

    protected function decodeJwtPayload(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = $parts[1];
        $payload = strtr($payload, '-_', '+/');
        $payload = base64_decode($payload, true);

        return json_decode($payload, true) ?: null;
    }
}
