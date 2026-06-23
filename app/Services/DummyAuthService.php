<?php

namespace App\Services;

use App\Models\TUser;
use Illuminate\Support\Facades\Hash;

class DummyAuthService
{
    public function attempt(string $username, string $password): ?array
    {
        $user = TUser::where('Username', $username)->first();

        if ($user && Hash::check($password, $user->Password)) {
            $userData = [
                'id' => $user->id,
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->Username,
                'role' => $user->jenis_user,
                'kode_prodi' => $user->kode_prodi,
                'nama_prodi' => $user->nama_prodi,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) . '&background=2f5597&color=fff&size=128',
                'akun_ina' => $user->akun_ina,
                'status' => $user->status_approve === 'y' ? 'approved' : 'pending',
            ];
            session(['auth_user' => $userData]);
            return $userData;
        }

        return null;
    }

    public function check(): bool
    {
        return session()->has('auth_user');
    }

    public function user(): ?array
    {
        return session('auth_user');
    }

    public function logout(): void
    {
        session()->forget('auth_user');
    }

    public function register(array $data): array
    {
        $user = TUser::create([
            'nip_nim' => $data['nip_nim'] ?? rand(10000, 99999),
            'nama_lengkap' => $data['name'],
            'email' => $data['email'],
            'Username' => $data['username'],
            'Password' => Hash::make($data['password']),
            'jenis_user' => 'Mahasiswa',
            'status_pegawai' => 'Mahasiswa',
            'kode_fs' => '13321002',
            'nama_fs' => 'FTTM',
            'status_aktif' => 'AKTIF',
            'status_approve' => 't',
            'akun_ina' => $data['akun_ina'] ?? null,
        ]);

        $userData = [
            'id' => $user->id,
            'name' => $user->nama_lengkap,
            'email' => $user->email,
            'username' => $user->Username,
            'role' => $user->jenis_user,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) . '&background=2f5597&color=fff&size=128',
            'akun_ina' => $user->akun_ina,
            'status' => 'pending',
        ];

        return $userData;
    }

    public function hasRole(string|array $roles): bool
    {
        $user = $this->user();
        if (!$user) return false;

        if (is_array($roles)) {
            return in_array($user['role'], $roles);
        }

        return $user['role'] === $roles;
    }

    public function allUsers(): array
    {
        return TUser::all()->toArray();
    }

    public function pendingUsers(): array
    {
        return TUser::where('status_approve', 't')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->nama_lengkap,
                    'email' => $u->email,
                    'username' => $u->Username,
                    'role' => $u->jenis_user,
                    'akun_ina' => $u->akun_ina,
                    'status' => 'pending',
                    'registered_at' => $u->tgl_create,
                ];
            })->toArray();
    }

    public function approveUser(int $id): bool
    {
        $user = TUser::find($id);
        if ($user) {
            $user->status_approve = 'y';
            $user->save();
            return true;
        }
        return false;
    }

    public function rejectUser(int $id, string $reason = ''): bool
    {
        $user = TUser::find($id);
        if ($user) {
            $user->status_aktif = 'NON AKTIF';
            $user->save();
            return true;
        }
        return false;
    }
}

