<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TUser;
use App\Models\TProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $authUser = session('auth_user');
        $query = TUser::query();

        if ($authUser['role'] === 'TU Prodi' && $authUser['kode_prodi']) {
            $query->where('kode_prodi', $authUser['kode_prodi']);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->through(function ($u) {
            return [
                'id'             => $u->id,
                'nip_nim'        => $u->nip_nim,
                'nama_lengkap'   => $u->nama_lengkap,
                'email'          => $u->email,
                'akun_ina'       => $u->akun_ina,
                'username'       => $u->username,
                'status_pegawai' => $u->status_pegawai,
                'jenis_user'     => $u->jenis_user,
                'kode_prodi'     => $u->kode_prodi,
                'nama_prodi'     => $u->nama_prodi,
                'kode_fs'        => $u->kode_fs,
                'nama_fs'        => $u->nama_fs,
                'strata'         => $u->strata,
                'thn_angkatan'   => $u->thn_angkatan,
                'status_aktif'   => $u->status_aktif,
                'status_approve' => $u->status_approve,
                'status_kaprodi' => $u->status_kaprodi,
            ];
        });

        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();

        return view('master.user', compact('users', 'prodis'));
    }

    public function edit($id)
    {
        $u = TUser::find((int) $id);
        if (!$u) {
            return response()->json(null, 404);
        }

        return response()->json([
            'id'             => $u->id,
            'nip_nim'        => $u->nip_nim,
            'nama_lengkap'   => $u->nama_lengkap,
            'email'          => $u->email,
            'akun_ina'       => $u->akun_ina,
            'username'       => $u->username,
            'status_pegawai' => $u->status_pegawai,
            'jenis_user'     => $u->jenis_user,
            'kode_prodi'     => $u->kode_prodi,
            'nama_prodi'     => $u->nama_prodi,
            'kode_fs'        => $u->kode_fs,
            'nama_fs'        => $u->nama_fs,
            'strata'         => $u->strata,
            'thn_angkatan'   => $u->thn_angkatan,
            'status_aktif'   => $u->status_aktif,
            'status_approve' => $u->status_approve,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_nim'        => 'required',
            'nama_lengkap'   => 'required',
            'email'          => 'required|email',
            'jenis_user'     => 'required',
            'status_pegawai' => 'required',
            'status_aktif'   => 'required',
            'status_approve' => 'required',
        ]);

        // Resolve prodi
        $kodeProdi = null;
        $namaProdi = null;
        if ($request->id_prodi) {
            $prodi     = TProdi::find($request->id_prodi);
            $kodeProdi = $prodi?->kode_prodi;
            $namaProdi = $prodi?->nama_prodi;
        }

        // Resolve FS prodi
        $kodeFs = null;
        $namaFs = null;
        if ($request->id_fs_prodi) {
            $fsProdi = TProdi::find($request->id_fs_prodi);
            $kodeFs  = $fsProdi?->kode_prodi;
            $namaFs  = $fsProdi?->nama_prodi;
        }

        TUser::create([
            'NIP_NIM'        => $request->nip_nim,
            'NAMA_LENGKAP'   => $request->nama_lengkap,
            'EMAIL'          => $request->email,
            'AKUN_INA'       => $request->akun_ina,
            'USERNAME'       => $request->username,
            'PASSWORD'       => $request->password ? Hash::make($request->password) : null,
            'JENIS_USER'     => $request->jenis_user,
            'STATUS_PEGAWAI' => $request->status_pegawai,
            'KODE_PRODI'     => $kodeProdi,
            'NAMA_PRODI'     => $namaProdi,
            'KODE_FS'        => $kodeFs,
            'NAMA_FS'        => $namaFs,
            'STRATA'         => $request->strata,
            'THN_ANGKATAN'   => $request->thn_angkatan,
            'STATUS_AKTIF'   => $request->status_aktif,
            'STATUS_APPROVE' => $request->status_approve,
            'TGL_CREATE'     => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nip_nim'        => 'required',
            'nama_lengkap'   => 'required',
            'email'          => 'required|email',
            'jenis_user'     => 'required',
            'status_pegawai' => 'required',
            'status_aktif'   => 'required',
            'status_approve' => 'required',
        ]);

        $user = TUser::find((int) $id);
        if ($user) {
            $kodeProdi = $user->kode_prodi;
            $namaProdi = $user->nama_prodi;
            if ($request->id_prodi) {
                $prodi     = TProdi::find($request->id_prodi);
                $kodeProdi = $prodi?->kode_prodi;
                $namaProdi = $prodi?->nama_prodi;
            }

            $kodeFs = $user->kode_fs;
            $namaFs = $user->nama_fs;
            if ($request->id_fs_prodi) {
                $fsProdi = TProdi::find($request->id_fs_prodi);
                $kodeFs  = $fsProdi?->kode_prodi;
                $namaFs  = $fsProdi?->nama_prodi;
            }

            $data = [
                'NIP_NIM'        => $request->nip_nim,
                'NAMA_LENGKAP'   => $request->nama_lengkap,
                'EMAIL'          => $request->email,
                'AKUN_INA'       => $request->akun_ina,
                'USERNAME'       => $request->username,
                'JENIS_USER'     => $request->jenis_user,
                'STATUS_PEGAWAI' => $request->status_pegawai,
                'KODE_PRODI'     => $kodeProdi,
                'NAMA_PRODI'     => $namaProdi,
                'KODE_FS'        => $kodeFs,
                'NAMA_FS'        => $namaFs,
                'STRATA'         => $request->strata,
                'THN_ANGKATAN'   => $request->thn_angkatan,
                'STATUS_AKTIF'   => $request->status_aktif,
                'STATUS_APPROVE' => $request->status_approve,
                'TGL_UPDATE'     => now(),
            ];

            if ($request->password) {
                $data['PASSWORD'] = Hash::make($request->password);
            }

            $oldStatus = $user->status_approve;
            $user->update($data);

            if ($oldStatus !== $user->status_approve) {
                if ($user->status_approve === 't') {
                    Notification::createForUser(
                        $user->id,
                        'Akun Disetujui',
                        'Selamat! Akun Anda (' . $user->nama_lengkap . ') telah disetujui oleh admin. Silakan login.',
                        'success',
                        route('login')
                    );
                } elseif ($user->status_approve === 'f') {
                    Notification::createForUser(
                        $user->id,
                        'Akun Ditolak',
                        'Maaf, akun Anda (' . $user->nama_lengkap . ') tidak disetujui. Silakan hubungi admin.',
                        'error',
                        null
                    );
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $user = TUser::find((int) $id);
        if ($user) {
            $user->delete();
        }
        return response()->json(['success' => true]);
    }
}