<?php

namespace App\Services;

use App\Models\TUser;
use Illuminate\Support\Facades\Hash;

class DummyAuthService
{
    public function attempt(string $username, ?string $password, bool $sso = false): ?array
    {
        $user = TUser::where('USERNAME', $username)
            ->where('STATUS_AKTIF', 'AKTIF')
            ->first();

        if ($user && ($sso || ($password !== null && Hash::check($password, $user->getAuthPassword())))) {
            $userData = $this->toSessionUser($user);
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
            'NIP_NIM' => $data['nip_nim'] ?? (string) rand(10000, 99999),
            'NAMA_LENGKAP' => $data['nama_lengkap'] ?? $data['name'] ?? 'User',
            'EMAIL' => $data['email'],
            'USERNAME' => $data['username'],
            'PASSWORD' => Hash::make($data['password']),
            'JENIS_USER' => 'Mahasiswa',
            'STATUS_PEGAWAI' => 'Mahasiswa',
            'KODE_FS' => '13321002',
            'NAMA_FS' => 'FTTM',
            'STATUS_AKTIF' => 'AKTIF',
            'STATUS_APPROVE' => 'f',
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
            'AKUN_INA' => $data['akun_ina'] ?? null,
        ]);

        return $this->toSessionUser($user, pending: true);
    }

    public function hasRole(string|array $roles): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        $userRoles = $user['roles'] ?? [$user['role'] ?? null];

        if (is_array($roles)) {
            return array_intersect($roles, $userRoles) !== [];
        }

        return in_array($roles, $userRoles, true);
    }

    public function allUsers(): array
    {
        return TUser::all()->toArray();
    }

    public function pendingUsers(): array
    {
        return TUser::where('STATUS_APPROVE', 'f')
            ->get()
            ->map(fn (TUser $u) => array_merge($this->toSessionUser($u, pending: true), [
                'registered_at' => $u->TGL_CREATE,
            ]))
            ->toArray();
    }

    public function approveUser(int $id): bool
    {
        $user = TUser::find($id);
        if ($user) {
            $user->STATUS_APPROVE = 't';
            $user->save();
            return true;
        }
        return false;
    }

    public function rejectUser(int $id, string $reason = ''): bool
    {
        $user = TUser::find($id);
        if ($user) {
            $user->STATUS_AKTIF = 'NON AKTIF';
            $user->save();
            return true;
        }
        return false;
    }

    private function toSessionUser(TUser $user, bool $pending = false): array
    {
        $nama = $user->NAMA_LENGKAP;

        $roles = $user->roles();
        $defaultRole = $user->defaultRole() ?? $user->JENIS_USER;

        return [
            'id' => $user->id,
            'nama_lengkap' => $nama,
            'nip_nim' => $user->NIP_NIM,
            'email' => $user->EMAIL,
            'Username' => $user->USERNAME,
            'role' => $defaultRole,
            'roles' => $roles,
            'strata' => $user->STRATA,
            'kode_prodi' => $user->KODE_PRODI,
            'nama_prodi' => $user->NAMA_PRODI,
            'kode_fs' => $user->KODE_FS,
            'nama_fs' => $user->NAMA_FS,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=2f5597&color=fff&size=128',
            'akun_ina' => $user->AKUN_INA,
            'status' => $pending ? 'pending' : ($user->STATUS_APPROVE === 't' ? 'approved' : 'pending'),
        ];
    }
}
