<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TUser;
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

        $user = $this->auth->register($request->only(['name', 'email', 'username', 'password', 'akun_ina']));

        $admins = TUser::where('jenis_user', 'Admin')->get();
        foreach ($admins as $admin) {
            Notification::createForUser(
                $admin->id,
                'Pengajuan User Baru',
                $user['nama_lengkap'] . ' (' . $user['Username'] . ') telah mendaftar. Silakan verifikasi.',
                'info',
                route('master.user.index')
            );
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan tunggu persetujuan admin.');
    }
}
