<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TMkProdi;
use App\Models\TProdi;
use App\Models\TTahapan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MkProdiController extends Controller
{
    /** Tampilan label tahapan (disimpan di DB tetap nilai asli TAHAPAN) */
    private const TAHAPAN_DISPLAY = [
        'tahap I'  => 'Ujian Kualifikasi',
        'tahap II' => 'Ujian Proposal',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
    ];

    private function tahapDisplay($raw): string
    {
        $raw = (string) $raw;
        return self::TAHAPAN_DISPLAY[$raw] ?? $raw;
    }

    public function index(Request $request)
    {
        $query = TMkProdi::query();

        if ($s = $request->get('kode_prodi')) {
            $query->where('KODE_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('nama_prodi')) {
            $query->where('NAMA_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('kode_mk')) {
            $query->where('KODE_MK', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('tahapan_sidang')) {
            // Pencarian bisa memakai label tampil (mis. "Ujian Proposal") maupun nilai asli ("tahap II")
            $matchedRaws = [];
            foreach (self::TAHAPAN_DISPLAY as $raw => $label) {
                if (stripos($label, $s) !== false || stripos($raw, $s) !== false) {
                    $matchedRaws[] = $raw;
                }
            }
            $query->where(function ($q) use ($s, $matchedRaws) {
                $q->where('TAHAPAN_SIDANG', 'like', '%' . $s . '%');
                foreach ($matchedRaws as $raw) {
                    $q->orWhere('TAHAPAN_SIDANG', $raw);
                }
            });
        }

        $items = $query->orderBy('KODE_PRODI', 'asc')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString()
            ->through(function ($p) {
                return [
                    'id' => $p->id,
                    'kode_prodi' => $p->kode_prodi,
                    'nama_prodi' => $p->nama_prodi,
                    'kode_mk' => $p->kode_mk,
                    'tahapan_sidang' => $p->tahapan_sidang,
                    'tahapan_display' => $this->tahapDisplay($p->tahapan_sidang),
                ];
            });

        if ($request->ajax()) {
            $tableHtml = view('master._mk_prodi_table', compact('items'))->render();
            return response()->json(['html' => $tableHtml]);
        }

        return view('master.mk-prodi', [
            'items' => $items,
            'prodiList' => $this->prodiList(),
            'tahapanList' => $this->tahapanList(),
        ]);
    }

    private function prodiList()
    {
        $user = session('auth_user');
        $query = TProdi::query();

        if (is_array($user) && ($user['role'] ?? '') === 'TU Prodi') {
            $query->whereIn('id', $user['id_prodi'] ?? []);
        } elseif (is_array($user) && !empty($user['kode_fs'])) {
            $query->where('KODE_FS', $user['kode_fs']);
        }

        return $query->orderBy('KODE_PRODI')->get()->map(function ($p) {
            return [
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
            ];
        });
    }

    private function tahapanList()
    {
        return TTahapan::orderBy('id')->get()->map(function ($t) {
            $raw = (string) $t->tahapan;
            return [
                'value' => $raw,
                'label' => $this->tahapDisplay($raw),
            ];
        });
    }

    public function create()
    {
        return view('master.mk-prodi-form', [
            'item' => null,
            'prodiList' => $this->prodiList(),
            'tahapanList' => $this->tahapanList(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required',
            'nama_prodi' => 'required',
            'kode_mk' => 'required',
            'tahapan_sidang' => [
                'required',
                Rule::unique('t_mk_prodi', 'TAHAPAN_SIDANG')->where('KODE_PRODI', $request->kode_prodi),
            ],
        ], [
            'tahapan_sidang.unique' => 'Kombinasi Kode Prodi dan Tahapan Sidang sudah terdaftar.',
        ]);

        TMkProdi::create([
            'KODE_PRODI' => $request->kode_prodi,
            'NAMA_PRODI' => $request->nama_prodi,
            'KODE_MK' => $request->kode_mk,
            'TAHAPAN_SIDANG' => $request->tahapan_sidang,
            'TGL_BUAT' => now(),
            'TGL_UPDATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.mk-prodi.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $p = TMkProdi::find((int) $id);
        if (!$p) abort(404);
        $item = [
            'id' => $p->id,
            'kode_prodi' => $p->kode_prodi,
            'nama_prodi' => $p->nama_prodi,
            'kode_mk' => $p->kode_mk,
            'tahapan_sidang' => $p->tahapan_sidang,
        ];

        if (request()->wantsJson()) {
            return response()->json($item);
        }

        return view('master.mk-prodi-form', [
            'item' => $item,
            'prodiList' => $this->prodiList(),
            'tahapanList' => $this->tahapanList(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_prodi' => 'required',
            'nama_prodi' => 'required',
            'kode_mk' => 'required',
            'tahapan_sidang' => [
                'required',
                Rule::unique('t_mk_prodi', 'TAHAPAN_SIDANG')
                    ->where('KODE_PRODI', $request->kode_prodi)
                    ->ignore($id),
            ],
        ], [
            'tahapan_sidang.unique' => 'Kombinasi Kode Prodi dan Tahapan Sidang sudah terdaftar.',
        ]);

        $p = TMkProdi::find((int) $id);
        if ($p) {
            $p->update([
                'KODE_PRODI' => $request->kode_prodi,
                'NAMA_PRODI' => $request->nama_prodi,
                'KODE_MK' => $request->kode_mk,
                'TAHAPAN_SIDANG' => $request->tahapan_sidang,
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.mk-prodi.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $p = TMkProdi::find((int) $id);
        if ($p) {
            $p->delete();
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.mk-prodi.index')->with('success', 'Data berhasil dihapus.');
    }

}