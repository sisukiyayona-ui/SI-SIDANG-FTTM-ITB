<?php

namespace App\Http\Controllers;

use App\Models\TUser;
use Illuminate\Http\Request;

class ApproveUserController extends Controller
{
    public function index()
    {
        $sessionUser = session('auth_user');

        $query = TUser::where('status_approve', 't');

        // TU Prodi hanya lihat user dari prodi-nya sendiri
        if ($sessionUser['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $sessionUser['kode_prodi']);
        }

        $users = $query->get()->map(function ($u) {
            return [
                'id'           => $u->id,
                'name'         => $u->nama_lengkap,
                'email'        => $u->email,
                'username'     => $u->Username,
                'role'         => $u->jenis_user,
                'akun_ina'     => $u->akun_ina,
                'status'       => 'pending',
                'registered_at' => $u->tgl_create,
            ];
        })->toArray();

        return view('approve.index', compact('users'));
    }

    public function approve($id)
    {
        $user = TUser::find((int) $id);
        if ($user) {
            $user->status_approve = 'y';
            $user->save();
        }
        return redirect()->route('approve.user')->with('success', 'User berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $user = TUser::find((int) $id);
        if ($user) {
            $user->status_aktif = 'NON AKTIF';
            $user->save();
        }
        return redirect()->route('approve.user')->with('success', 'User berhasil ditolak.');
    }
}

