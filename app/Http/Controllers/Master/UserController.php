<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TUser;
use App\Models\TProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = TUser::all()->map(function ($u) {
            return [
                'id'             => $u->id,
                'nip_nim'        => $u->nip_nim,
                'nama_lengkap'   => $u->nama_lengkap,
                'email'          => $u->email,
                'akun_ina'       => $u->akun_ina,
                'Username'       => $u->Username,
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
            ];
        });

        $prodis = TProdi::where('status_aktif', 'AKTIF')->get();

        return view('master.user', compact('users', 'prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_nim'        => 'required',
            'nama_lengkap'   => 'required',
            'email'          => 'required|email',
            'jenis_user'     => 'required',
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

        TUser::create([
            'nip_nim'        => $request->nip_nim,
            'nama_lengkap'   => $request->nama_lengkap,
            'email'          => $request->email,
            'akun_ina'       => $request->akun_ina,
            'Username'       => $request->Username,
            'Password'       => $request->Password ? Hash::make($request->Password) : null,
            'status_pegawai' => $request->status_pegawai,
            'jenis_user'     => $request->jenis_user,
            'kode_prodi'     => $kodeProdi,
            'nama_prodi'     => $namaProdi,
            'kode_fs'        => '13321002',
            'nama_fs'        => 'FTTM',
            'strata'         => $request->strata,
            'thn_angkatan'   => $request->thn_angkatan,
            'status_aktif'   => $request->status_aktif,
            'status_approve' => $request->status_approve,
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

            $data = [
                'nip_nim'        => $request->nip_nim,
                'nama_lengkap'   => $request->nama_lengkap,
                'email'          => $request->email,
                'akun_ina'       => $request->akun_ina,
                'Username'       => $request->Username,
                'status_pegawai' => $request->status_pegawai,
                'jenis_user'     => $request->jenis_user,
                'kode_prodi'     => $kodeProdi,
                'nama_prodi'     => $namaProdi,
                'kode_fs'        => '13321002',
                'nama_fs'        => 'FTTM',
                'strata'         => $request->strata,
                'thn_angkatan'   => $request->thn_angkatan,
                'status_aktif'   => $request->status_aktif,
                'status_approve' => $request->status_approve,
                'tgl_update'     => now(),
            ];

            if ($request->Password) {
                $data['Password'] = Hash::make($request->Password);
            }

            $user->update($data);
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