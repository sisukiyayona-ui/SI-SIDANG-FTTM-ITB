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
    public function index(Request $request)
    {
        $authUser = session('auth_user');
        $query = TUser::query();

        if ($authUser['role'] === 'TU Prodi' && $authUser['kode_prodi']) {
            $query->where('kode_prodi', $authUser['kode_prodi']);
        }

        // Server-side search/filter
        if ($searchNip = $request->get('nip_nim')) {
            $query->where('NIP_NIM', 'like', '%' . $searchNip . '%');
        }
        if ($searchNama = $request->get('nama_lengkap')) {
            $query->where('NAMA_LENGKAP', 'like', '%' . $searchNama . '%');
        }
        if ($searchEmail = $request->get('email')) {
            $query->where('EMAIL', 'like', '%' . $searchEmail . '%');
        }
        if ($searchStatusPegawai = $request->get('status_pegawai')) {
            $query->where('STATUS_PEGAWAI', $searchStatusPegawai);
        }
        if ($searchJenisUser = $request->get('jenis_user')) {
            $query->where('JENIS_USER', $searchJenisUser);
        }
        if ($searchProdi = $request->get('nama_prodi')) {
            $query->where('NAMA_PRODI', 'like', '%' . $searchProdi . '%');
        }
        if ($searchStatusAktif = $request->get('status_aktif')) {
            $query->where('STATUS_AKTIF', $searchStatusAktif);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString()->through(function ($u) {
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
            'status_kaprodi' => $u->status_kaprodi,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_nim'        => 'required',
            'nama_lengkap'   => 'required',
            'email'          => 'required|email',
            'username'       => 'required|unique:t_user,USERNAME',
            'password'       => 'nullable|min:4',
            'jenis_user'     => 'required',
            'status_pegawai' => 'nullable',
            'status_aktif'   => 'required',
            'status_approve' => 'required|in:t,f',
        ]);

        [$kodeProdi, $namaProdi] = $this->resolveProdi($request);
        [$kodeFs, $namaFs] = $this->resolveFs($request);

        $hashedPassword = $request->filled('password') ? Hash::make($request->password) : null;

        TUser::create([
            'NIP_NIM'         => $request->nip_nim,
            'NAMA_LENGKAP'    => $request->nama_lengkap,
            'EMAIL'           => $request->email,
            'AKUN_INA'        => $request->akun_ina,
            'USERNAME'        => $request->username,
            'PASSWORD'        => $hashedPassword,
            'JENIS_USER'      => $request->jenis_user,
            'STATUS_PEGAWAI'  => $request->status_pegawai,
            'KODE_PRODI'      => $kodeProdi,
            'NAMA_PRODI'      => $namaProdi,
            'KODE_FS'         => $kodeFs,
            'NAMA_FS'         => $namaFs,
            'STRATA'          => $request->strata,
            'THN_ANGKATAN'    => $request->thn_angkatan,
            'STATUS_AKTIF'    => $request->status_aktif,
            'STATUS_APPROVE'  => $request->status_approve,
            'STATUS_KAPRODI'  => $request->status_kaprodi,
            'TGL_CREATE'      => now(),
            'TGL_UPDATE'      => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nip_nim'        => 'required',
            'nama_lengkap'   => 'required',
            'email'          => 'required|email',
            'username'       => 'required|unique:t_user,USERNAME,' . (int) $id . ',id',
            'password'       => 'nullable|min:4',
            'jenis_user'     => 'required',
            'status_pegawai' => 'nullable',
            'status_aktif'   => 'required',
            'status_approve' => 'required|in:t,f',
        ]);

        $user = TUser::find((int) $id);
        if ($user) {
            [$kodeProdi, $namaProdi] = $this->resolveProdi($request, $user);
            [$kodeFs, $namaFs] = $this->resolveFs($request, $user);

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
                'STATUS_KAPRODI' => $request->status_kaprodi,
                'TGL_UPDATE'     => now(),
            ];

            if ($request->filled('password')) {
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

    private function resolveProdi(Request $request, ?TUser $existing = null): array
    {
        $authUser = session('auth_user');

        if ($request->id_prodi) {
            $prodi = TProdi::find($request->id_prodi);
            return [$prodi?->kode_prodi, $prodi?->nama_prodi];
        }

        if ($existing && $existing->kode_prodi) {
            return [$existing->kode_prodi, $existing->nama_prodi];
        }

        if (!empty($authUser['kode_prodi'])) {
            return [$authUser['kode_prodi'], $authUser['nama_prodi'] ?? null];
        }

        return [null, null];
    }

    private function resolveFs(Request $request, ?TUser $existing = null): array
    {
        // Form selalu mengirim hidden kode_fs=164 dan nama_fs=FTTM
        if ($request->filled('kode_fs')) {
            return [$request->kode_fs, $request->nama_fs ?? 'FTTM'];
        }

        if ($existing && $existing->kode_fs) {
            return [$existing->kode_fs, $existing->nama_fs ?: 'FTTM'];
        }

        return ['164', 'FTTM'];
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