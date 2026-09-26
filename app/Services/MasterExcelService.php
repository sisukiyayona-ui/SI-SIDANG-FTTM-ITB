<?php

namespace App\Services;

use App\Models\TPointPenilaian;
use App\Models\TProdi;
use App\Models\TSyaratSidang;
use App\Models\TTahapan;
use App\Models\TFs;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MasterExcelService
{
    public const HEADERS = [
        'persyaratan' => [
            'PROGRAM STUDI',
            'NAMA PERSYARATAN',
            'TAHAPAN SIDANG',
            'STRATA',
        ],
        'penilaian' => [
            'PROGRAM STUDI',
            'PARAMETER PENILAIAN',
            'NO FORM',
            'TAHAPAN SIDANG',
            'STRATA',
            'STATUS CATATAN (y/t)',
            'KETERANGAN',
        ],
        'prodi' => [
            'FAKULTAS',
            'KODE PRODI',
            'NAMA PRODI',
        ],
        'fakultas' => [
            'KODE FAKULTAS',
            'NAMA FAKULTAS',
        ],
    ];

    /**
     * Selamat jalan untuk kolom pertama: judul lama maupun file buatan user
     * tetap dikenali sebagai baris header supaya datanya tidak ikut terbaca.
     */
    private const HEADER_ALIASES = [
        'prodi' => ['FAKULTAS', 'KODE FAKULTAS', 'NAMA FAKULTAS'],
    ];

    public const FILES = [
        'persyaratan' => 'template_persyaratan',
        'penilaian' => 'template_penilaian',
        'prodi' => 'template_prodi',
        'fakultas' => 'template_fakultas',
    ];

    /**
     * Valid strata yang boleh dipakai (persyaratan & penilaian).
     */
    public const VALID_STRATA = ['S1', 'S2', 'S3'];

    /**
     * Digit pertama kode prodi yang wajib cocok dengan strata.
     */
    public const STRATA_DIGIT = [
        'S1' => '1',
        'S2' => '2',
        'S3' => '3',
    ];

    /**
     * Label ramah yang ditampilkan untuk nilai tahapan di t_tahapan.
     * Key = TAHAPAN (canonical), Value = label tampilan.
     */
    public const TAHAPAN_LABELS = [
        'tahap I' => 'Ujian Kualifikasi',
        'tahap 1' => 'Ujian Kualifikasi',
        'tahap II' => 'Ujian Proposal',
        'tahap 2' => 'Ujian Proposal',
        'tahap III' => 'Tahap III',
        'tahap 3' => 'Tahap III',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
        'tahap 4' => 'Sidang Terbuka / Tertutup',
        'SK I' => 'SK I',
        'SK II' => 'SK II',
        'SK III' => 'SK III',
        'SK IV' => 'SK IV',
    ];

    /**
     * Normalisasi input user (kode/label/typo ringan) -> nilai TAHAPAN kanonik.
     * Mengembalikan null bila tidak ada padanan di t_tahapan.
     */
    public static function canonicalTahapan($value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $norm = static function ($s) {
            $s = mb_strtolower(trim((string) $s));
            $s = preg_replace('/\s+/', ' ', $s);
            return $s;
        };

        $needle = $norm($raw);

        foreach (TTahapan::all() as $t) {
            if ($norm($t->TAHAPAN) === $needle) {
                return $t->TAHAPAN;
            }
        }

        // Cocokkan lewat label tampilan (mis. "Ujian Kualifikasi" -> "tahap I")
        foreach (self::TAHAPAN_LABELS as $canonical => $label) {
            if ($norm($label) === $needle && TTahapan::where('TAHAPAN', $canonical)->exists()) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * Normalisasi strata (terima "s3"/"S 3"/"3") -> S1|S2|S3, atau null bila tidak valid.
     */
    public static function canonicalStrata($value): ?string
    {
        $raw = strtoupper(preg_replace('/\s+/', '', trim((string) $value)));
        if ($raw === '') {
            return null;
        }
        if (!preg_match('/^S?([123])$/', $raw, $m)) {
            return null;
        }
        return 'S' . $m[1];
    }

    /**
     * Validasi satu baris persyaratan/penilaian. Mengembalikan
     * ['result' => 'ok'|'error'|'duplicate', 'message' => string, ...data ternormalisasi].
     */
    /**
     * Cari prodi dari PROGRAM STUDI yang diisi user.
     *
     * Nama prodi tidak unik (mis. "TEKNIK GEOFISIKA" ada di S1/S2/S3), jadi
     * pencarian dibatasi pada strata baris tersebut lewat digit pertama kode.
     * Kode prodi lama tetap diterima sebagai cadangan.
     */
    public static function resolveProdi(string $input, ?string $strata = null): ?TProdi
    {
        $value = trim($input);
        if ($value === '') {
            return null;
        }

        $digit = $strata !== null ? (self::STRATA_DIGIT[$strata] ?? null) : null;

        $byName = TProdi::query()
            ->whereRaw('UPPER(TRIM(NAMA_PRODI)) = ?', [mb_strtoupper($value)])
            ->when($digit, fn ($q) => $q->where('KODE_PRODI', 'like', $digit . '%'))
            ->first();

        if ($byName) {
            return $byName;
        }

        $byCode = TProdi::query()->where('KODE_PRODI', $value)->first();
        if ($byCode && ($digit === null || substr($byCode->kode_prodi, 0, 1) === $digit)) {
            return $byCode;
        }

        return null;
    }

    /**
     * Daftar strata tempat program studi ini terdaftar (untuk pesan error).
     */
    private static function prodiStrataList(string $input): array
    {
        return TProdi::query()
            ->whereRaw('UPPER(TRIM(NAMA_PRODI)) = ?', [mb_strtoupper(trim($input))])
            ->orderBy('KODE_PRODI')
            ->pluck('KODE_PRODI')
            ->map(fn ($kode) => 'S' . substr($kode, 0, 1))
            ->unique()
            ->values()
            ->all();
    }

    public static function validateItemRow(array $values, string $type, array $seen = [], $ignoreId = null): array
    {
        if ($type === 'penilaian') {
            [$programStudi, $nama, $noForm, $tahapan, $strata, $statusCatatan, $keterangan] = $values + array_fill(0, 7, '');
        } else {
            [$programStudi, $nama, $tahapan, $strata] = $values + array_fill(0, 4, '');
            $noForm = $statusCatatan = $keterangan = '';
        }

        $clean = [
            'program_studi' => trim((string) $programStudi),
            'nama' => trim((string) $nama),
            'no_form' => trim((string) $noForm),
            'tahapan' => trim((string) $tahapan),
            'strata' => trim((string) $strata),
            // Import selalu membuat data aktif, jadi tidak perlu kolom status.
            'status_aktif' => 'AKTIF',
            'status_catatan' => self::canonicalStatusCatatan($statusCatatan),
            'keterangan' => trim((string) $keterangan),
        ];

        // Nilai status catatan yang tidak dikenal ditolak, bukan dipaksa jadi default,
        // supaya salah ketik pada template tidak diam-diam tersimpan.
        $catatanRaw = mb_strtolower(preg_replace('/\s+/', '', trim((string) $statusCatatan)));
        if ($catatanRaw !== '' && !in_array($catatanRaw, ['y', 't'], true)) {
            return ['result' => 'error', 'message' => 'STATUS CATATAN "' . $statusCatatan . '" tidak valid (gunakan y atau t)'] + $clean;
        }

        if ($clean['program_studi'] === '') {
            return ['result' => 'error', 'message' => 'PROGRAM STUDI kosong'] + $clean;
        }
        if ($clean['nama'] === '') {
            return ['result' => 'error', 'message' => ($type === 'penilaian' ? 'PARAMETER PENILAIAN' : 'NAMA PERSYARATAN') . ' kosong'] + $clean;
        }

        $strataVal = self::canonicalStrata($clean['strata']);
        if ($strataVal === null) {
            return ['result' => 'error', 'message' => 'Strata "' . ($clean['strata'] ?: '-') . '" tidak valid (hanya S1/S2/S3)'] + $clean;
        }
        $clean['strata'] = $strataVal;

        $prodi = self::resolveProdi($clean['program_studi'], $strataVal);
        if (!$prodi) {
            $message = 'Program studi "' . $clean['program_studi'] . '" tidak terdaftar';
            $otherStrata = self::prodiStrataList($clean['program_studi']);
            if ($otherStrata && !in_array($strataVal, $otherStrata, true)) {
                $message .= ' pada strata ' . $strataVal . ' (tersedia di ' . implode(', ', $otherStrata) . ')';
            }

            return ['result' => 'error', 'message' => $message] + $clean;
        }
        $clean['prodi'] = $prodi;
        $clean['kode_prodi'] = $prodi->kode_prodi;
        $clean['nama_prodi'] = $prodi->nama_prodi;

        $tahapanVal = self::canonicalTahapan($clean['tahapan']);
        if ($tahapanVal === null) {
            $valid = self::tahapanOptions();
            return ['result' => 'error', 'message' => 'Tahapan "' . ($clean['tahapan'] ?: '-') . '" tidak terdaftar di master tahapan' . ($valid ? ' (pilihan: ' . implode(', ', $valid) . ')' : '')] + $clean;
        }
        $clean['tahapan'] = $tahapanVal;

        // Duplikat di dalam file yang sedang diupload
        $dupKey = $prodi->id . '|' . mb_strtolower($tahapanVal) . '|' . mb_strtolower($clean['nama']);
        if (isset($seen[$dupKey])) {
            return ['result' => 'duplicate', 'message' => 'Duplikat dari baris lain di file yang sama'] + $clean;
        }

        // Duplikat di database (prodi + tahapan + nama sama)
        $namaColumn = $type === 'penilaian' ? 'PENILAIAN' : 'NAMA_PERSYARATAN';
        $query = ($type === 'penilaian' ? TPointPenilaian::query() : TSyaratSidang::query())
            ->where('KODE_PRODI', $clean['kode_prodi'])
            ->where('TAHAPAN_SIDANG', $tahapanVal)
            ->whereRaw('LOWER(' . $namaColumn . ') = ?', [mb_strtolower($clean['nama'])]);

        if ($ignoreId) {
            $query->where('id', '!=', (int) $ignoreId);
        }

        if ($query->exists()) {
            return ['result' => 'duplicate', 'message' => 'Sudah ada di database (prodi + tahapan + nama sama)'] + $clean;
        }

        return ['result' => 'ok', 'message' => 'Siap disimpan'] + $clean;
    }

    /**
     * Daftar nilai tahapan yang valid (dari master t_tahapan) untuk strata tertentu.
     */
    public static function tahapanOptions(?string $strata = null): array
    {
        $query = TTahapan::query()->orderBy('id');
        if ($strata) {
            $query->where('STRATA', $strata);
        }

        return $query->pluck('TAHAPAN')->toArray();
    }

    /**
     * Daftar nilai tahapan yang dipakai sebagai pilihan (dropdown) di template Excel.
     * Mengambil label tampilan yang sama dengan select list di form.
     */
    public static function tahapanDropdownItems(): array
    {
        $items = [];
        foreach (self::tahapanOptions() as $value) {
            $items[] = self::tahapanLabel($value);
        }

        return array_values(array_unique($items));
    }

    public static function template($type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = self::HEADERS[$type];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Baris judul
        $sheet->setCellValue('A1', strtoupper($type) . ' - TEMPLATE IMPORT DATA');
        $sheet->mergeCells('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Baris header
        $headerRow = 3;
        $tahapanColumn = null;
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $cellCoordinate = $columnLetter . $headerRow;
            $sheet->setCellValue($cellCoordinate, $header);
            $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
            $sheet->getStyle($cellCoordinate)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFD9E1F2');
            $sheet->getColumnDimension($columnLetter)->setWidth(25);

            if ($header === 'TAHAPAN SIDANG') {
                $tahapanColumn = $columnLetter;
            }
        }

        $sheet->freezePane('A' . ($headerRow + 1));

        // Kolom Tahapan Sidang memakai dropdown sesuai Data Master Persyaratan
        if ($tahapanColumn !== null) {
            $validation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setFormula1('"' . implode(',', self::tahapanDropdownItems()) . '"');
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setPromptTitle('Tahapan Sidang');
            $validation->setPrompt('Pilih dari daftar Data Master Persyaratan');
            $validation->setErrorTitle('Tahapan tidak valid');
            $validation->setError('Pilih Tahapan dari daftar yang tersedia.');
            $sheet->setDataValidation(
                $tahapanColumn . ($headerRow + 1) . ':' . $tahapanColumn . ($headerRow + 500),
                $validation
            );
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        return response()->streamDownload(function () use ($tempFile) {
            readfile($tempFile);
            unlink($tempFile);
        }, self::FILES[$type] . '_' . date('Ymd_His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Baca file Excel upload (hanya dari temp file PHP, tidak disimpan ke folder Laravel).
     * Baris header dicari otomatis supaya file hasil download template maupun
     * file buatan user bisa sama-sama terbaca.
     */
    public static function parse($type, $file): array
    {
        $headers = self::HEADERS[$type];

        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $norm = function ($value) {
            return strtoupper(preg_replace('/\s+/', ' ', trim((string) $value)));
        };
        $firstHeaders = array_map($norm, self::HEADER_ALIASES[$type] ?? []);
        $firstHeaders[] = $norm($headers[0]);

        $out = [];
        $rowNumber = 0;
        $headerFound = false;

        foreach ($rows as $row) {
            $rowNumber++;
            $values = array_values($row);
            $vals = [];
            for ($i = 0; $i < count($headers); $i++) {
                $vals[] = trim((string) ($values[$i] ?? ''));
            }

            if (!$headerFound) {
                // Lewati judul/keterangan di atas baris header.
                if (in_array($norm($values[0] ?? ''), $firstHeaders, true)) {
                    $headerFound = true;
                }
                continue;
            }

            if (count(array_filter($vals, fn ($v) => $v !== '')) === 0) {
                continue;
            }

            $out[] = ['row' => $rowNumber, 'values' => $vals];
        }

        return $out;
    }

    public static function import($type, $file, $user): array
    {
        $return = ['inserted' => 0, 'skipped' => 0, 'errors' => [], 'seen' => []];

        foreach (self::parse($type, $file) as $row) {
            try {
                self::{'store' . ucfirst($type)}($row['values'], $user, $row['row'], $return);
                $return['inserted']++;
            } catch (\Throwable $e) {
                $return['skipped']++;
                $return['errors'][] = 'Baris ' . $row['row'] . ': ' . $e->getMessage();
            }
        }

        unset($return['seen']);

        return $return;
    }

    /**
     * Preview import: baca file, validasi tiap baris, kembalikan data + statusnya.
     * File tidak disimpan ke folder Laravel (hanya dibaca dari temp file PHP).
     */
    public static function previewItems(string $type, $file, array $user = []): array
    {
        $seen = [];
        $rows = [];

        foreach (self::parse($type, $file) as $parsed) {
            $values = $parsed['values'];

            if (($user['role'] ?? '') === 'TU Prodi') {
                $values[0] = $user['kode_prodi'] ?? ($values[0] ?? '');
            }

            $check = self::validateItemRow($values, $type, $seen);

            if ($check['result'] === 'ok') {
                $seen[self::dupKey($check)] = true;
            }

            $row = [
                'row' => $parsed['row'],
                'program_studi' => $check['program_studi'],
                'kode_prodi' => $check['kode_prodi'] ?? '',
                'nama_prodi' => $check['nama_prodi'] ?? '',
                'nama' => $check['nama'],
                'tahapan' => $check['tahapan'],
                'tahapan_label' => self::tahapanLabel($check['tahapan']),
                'strata' => $check['strata'],
                'status_aktif' => $check['status_aktif'],
                'keterangan' => $check['keterangan'],
                'result' => $check['result'],
                'message' => $check['message'],
            ];

            if ($type === 'penilaian') {
                $row['no_form'] = $check['no_form'];
                $row['status_catatan'] = $check['status_catatan'];
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public static function tahapanLabel(?string $tahapan): string
    {
        if ($tahapan === null || $tahapan === '') {
            return '-';
        }

        return self::TAHAPAN_LABELS[$tahapan] ?? $tahapan;
    }

    /**
     * Simpan hasil import dari preview. $rows berisi data hasil preview/edit user.
     * Validasi diulang di sini (tidak hanya di preview) agar data yang diubah
     * saat preview tetap aman, dan file tidak perlu disimpan di folder Laravel.
     */
    public static function storeItems(string $type, array $rows, array $user = []): array
    {
        $seen = [];
        $results = [];
        $inserted = 0;
        $failed = 0;

        // Semua atau tidak sama sekali: satu baris gagal berarti seluruh import
        // dibatalkan, tidak ada baris pun yang masuk database.
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $values = self::toValues($row, $type);

                if (($user['role'] ?? '') === 'TU Prodi') {
                    $values[0] = $user['kode_prodi'] ?? ($values[0] ?? '');
                }

                $check = self::validateItemRow($values, $type, $seen, $row['id'] ?? null);

                $result = [
                    'row' => $row['row'] ?? ($index + 1),
                    'program_studi' => $check['program_studi'],
                    'kode_prodi' => $check['kode_prodi'] ?? '',
                    'nama' => $check['nama'],
                    'tahapan' => $check['tahapan'],
                    'strata' => $check['strata'],
                ];

                if ($check['result'] !== 'ok') {
                    $result['status'] = 'failed';
                    $result['message'] = $check['message'];
                    $failed++;
                    $results[] = $result;
                    continue;
                }

                try {
                    self::createItem($type, $check);
                    $seen[self::dupKey($check)] = true;
                    $result['status'] = 'success';
                    $result['message'] = 'Berhasil disimpan';
                    $inserted++;
                } catch (\Throwable $e) {
                    $result['status'] = 'failed';
                    $result['message'] = $e->getMessage();
                    $failed++;
                }

                $results[] = $result;
            }

            $rolledBack = $failed > 0;

            if ($rolledBack) {
                DB::rollBack();
                $results = self::markRolledBack($results);
                $inserted = 0;
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'inserted' => $inserted,
            'failed' => $failed,
            'results' => $results,
            'rolled_back' => $rolledBack,
        ];
    }

    /**
     * Setelah rollback tidak ada baris yang tersimpan, jadi status "success"
     * pada hasil harus diganti supaya tidak berbohong ke user.
     */
    private static function markRolledBack(array $results): array
    {
        foreach ($results as &$r) {
            if (($r['status'] ?? '') === 'success') {
                $r['status'] = 'failed';
                $r['message'] = 'Dibatalkan — ada baris lain yang gagal, tidak ada data yang disimpan';
            }
        }
        unset($r);

        return $results;
    }

    private static function createItem(string $type, array $check): void
    {
        $prodi = $check['prodi'];

        if ($type === 'penilaian') {
            TPointPenilaian::create([
                'PENILAIAN' => $check['nama'],
                'ID_PRODI' => $prodi->id,
                'KODE_PRODI' => $prodi->kode_prodi,
                'NAMA_PRODI' => $prodi->nama_prodi,
                'NO_FORM' => $check['no_form'],
                'TAHAPAN_SIDANG' => $check['tahapan'],
                'STRATA' => $check['strata'],
                'STATUS_AKTIF' => $check['status_aktif'],
                'STATUS_CATATAN' => $check['status_catatan'],
                'KETERANGAN' => $check['keterangan'],
                'TGL_CREATE' => now(),
            ]);

            return;
        }

        TSyaratSidang::create([
            'NAMA_PERSYARATAN' => $check['nama'],
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => $prodi->kode_prodi,
            'NAMA_PRODI' => $prodi->nama_prodi,
            'TAHAPAN_SIDANG' => $check['tahapan'],
            'STRATA' => $check['strata'],
            'STATUS_AKTIF' => $check['status_aktif'],
            'TGL_CREATE' => now(),
        ]);
    }

    /**
     * Ubah payload hasil preview (field camelCase) menjadi array kolom sesuai template.
     */
    private static function toValues(array $row, string $type): array
    {
        $programStudi = $row['program_studi'] ?? ($row['kode_prodi'] ?? '');

        if ($type === 'penilaian') {
            return [
                $programStudi,
                $row['penilaian'] ?? ($row['nama'] ?? ''),
                $row['no_form'] ?? '',
                $row['tahapan'] ?? ($row['tahapan_sidang'] ?? ''),
                $row['strata'] ?? '',
                $row['status_catatan'] ?? 't',
                $row['keterangan'] ?? '',
            ];
        }

        return [
            $programStudi,
            $row['nama_persyaratan'] ?? ($row['nama'] ?? ''),
            $row['tahapan'] ?? ($row['tahapan_sidang'] ?? ''),
            $row['strata'] ?? '',
        ];
    }

    private static function storePersyaratan(array $v, $user, int $row, array &$return): void
    {
        if (($user['role'] ?? '') === 'TU Prodi') {
            $v[0] = $user['kode_prodi'] ?? ($v[0] ?? '');
        }

        $check = self::validateItemRow($v, 'persyaratan', $return['seen'] ?? []);

        if ($check['result'] !== 'ok') {
            throw new \Exception($check['message']);
        }

        $return['seen'][self::dupKey($check)] = true;
        self::createItem('persyaratan', $check);
    }

    private static function storePenilaian(array $v, $user, int $row, array &$return): void
    {
        if (($user['role'] ?? '') === 'TU Prodi') {
            $v[0] = $user['kode_prodi'] ?? ($v[0] ?? '');
        }

        $check = self::validateItemRow($v, 'penilaian', $return['seen'] ?? []);

        if ($check['result'] !== 'ok') {
            throw new \Exception($check['message']);
        }

        $return['seen'][self::dupKey($check)] = true;
        self::createItem('penilaian', $check);
    }

    public static function dupKey(array $check): string
    {
        $idProdi = $check['prodi']->id ?? $check['kode_prodi'];

        return $idProdi . '|' . mb_strtolower($check['tahapan']) . '|' . mb_strtolower($check['nama']);
    }

    public static function canonicalStatus($value): string
    {
        $raw = strtoupper(trim((string) $value));

        return str_contains($raw, 'NON') ? 'NON AKTIF' : 'AKTIF';
    }

    public static function canonicalStatusCatatan($value): string
    {
        $raw = strtolower(trim((string) $value));

        return in_array($raw, ['y', 'ya', 'true', '1'], true) ? 'y' : 't';
    }

    /**
     * Kolom FAKULTAS diisi user dengan nama, kode juga tetap diterima.
     * Nama diprioritaskan karena itulah yang diketik user.
     */
    public static function resolveFakultas(string $input): ?TFs
    {
        $val = trim($input);
        if ($val === '') {
            return null;
        }

        $byName = TFs::whereRaw('UPPER(TRIM(NAMA_FS)) = ?', [mb_strtoupper($val)])->first();
        if ($byName) {
            return $byName;
        }

        return TFs::whereRaw('UPPER(TRIM(KODE_FS)) = ?', [mb_strtoupper($val)])->first();
    }

    private static function storeProdi(array $v, $user, int $row, array &$return): void
    {
        [$fakultas, $kode, $nama] = $v;

        if ($nama === '') {
            throw new \Exception('NAMA PRODI kosong');
        }

        if (TProdi::where('KODE_PRODI', $kode)->exists()) {
            throw new \Exception('Kode prodi ' . ($kode ?: '-') . ' sudah terdaftar');
        }

        $fs = self::resolveFakultas($fakultas);
        if (!$fs) {
            throw new \Exception('Fakultas ' . ($fakultas ?: '-') . ' tidak terdaftar');
        }

        TProdi::create([
            'KODE_PRODI' => $kode,
            'NAMA_PRODI' => $nama,
            'STATUS_AKTIF' => 'AKTIF',
            'KODE_FS' => $fs->KODE_FS,
            'NAMA_FS' => $fs->NAMA_FS,
            'TGL_CREATE' => now(),
        ]);
    }

    private static function storeFakultas(array $v, $user, int $row, array &$return): void
    {
        [$kode, $nama] = $v;

        if ($kode === '' || $nama === '') {
            throw new \Exception('KODE FAKULTAS dan NAMA FAKULTAS wajib diisi');
        }

        if (TFs::where('KODE_FS', $kode)->exists()) {
            throw new \Exception('Kode fakultas ' . $kode . ' sudah terdaftar');
        }

        TFs::create([
            'KODE_FS' => $kode,
            'NAMA_FS' => $nama,
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
        ]);
    }
}