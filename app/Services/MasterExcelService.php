<?php

namespace App\Services;

use App\Models\TPointPenilaian;
use App\Models\TProdi;
use App\Models\TSyaratSidang;
use App\Models\TFs;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MasterExcelService
{
    public const HEADERS = [
        'persyaratan' => [
            'KODE PRODI',
            'NAMA PERSYARATAN',
            'TAHAPAN SIDANG',
            'STRATA',
            'STATUS AKTIF',
        ],
        'penilaian' => [
            'KODE PRODI',
            'PENILAIAN',
            'NO FORM',
            'TAHAPAN SIDANG',
            'STRATA',
            'STATUS AKTIF',
            'STATUS CATATAN (y/t)',
            'KETERANGAN',
        ],
        'prodi' => [
            'KODE PRODI',
            'NAMA PRODI',
            'STATUS AKTIF',
        ],
        'fakultas' => [
            'KODE FAKULTAS',
            'NAMA FAKULTAS',
        ],
    ];

    public const FILES = [
        'persyaratan' => 'template_persyaratan',
        'penilaian' => 'template_penilaian',
        'prodi' => 'template_prodi',
        'fakultas' => 'template_fakultas',
    ];

    public static function template($type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = self::HEADERS[$type];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $cellCoordinate = $columnLetter . '1';
            $sheet->setCellValue($cellCoordinate, $header);
            $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
            $sheet->getStyle($cellCoordinate)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFD9E1F2');
        }

        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($columnLetter)->setWidth(25);
        }

        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx') . '.xlsx';
        $writer->save($tempFile);

        return response()->streamDownload(function () use ($tempFile) {
            readfile($tempFile);
            unlink($tempFile);
        }, self::FILES[$type] . '_' . date('Ymd_His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public static function import($type, $file, $user): array
    {
        $headers = self::HEADERS[$type];

        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $return = ['inserted' => 0, 'skipped' => 0, 'errors' => []];
        $rowNumber = 0;

        foreach ($rows as $row) {
            $rowNumber++;
            if ($rowNumber === 1) {
                continue;
            }

            $values = array_values($row);
            $vals = [];
            for ($i = 0; $i < count($headers); $i++) {
                $vals[] = trim((string) ($values[$i] ?? ''));
            }

            if (count(array_filter($vals, fn ($v) => $v !== '')) === 0) {
                continue;
            }

            try {
                self::{'store' . ucfirst($type)}($vals, $user, $rowNumber, $return);
                $return['inserted']++;
            } catch (\Throwable $e) {
                $return['skipped']++;
                $return['errors'][] = 'Baris ' . $rowNumber . ': ' . $e->getMessage();
            }
        }

        return $return;
    }

    private static function storePersyaratan(array $v, $user, int $row, array &$return): void
    {
        [$kodeProdi, $nama, $tahapan, $strata, $status] = $v;

        if ($user['role'] === 'TU Prodi') {
            $kodeProdi = $user['kode_prodi'];
        }

        if ($nama === '') {
            throw new \Exception('NAMA PERSYARATAN kosong');
        }

        $prodi = TProdi::where('KODE_PRODI', $kodeProdi)->first();
        if (!$prodi) {
            throw new \Exception('Kode prodi ' . ($kodeProdi ?: '-') . ' tidak terdaftar');
        }

        TSyaratSidang::create([
            'NAMA_PERSYARATAN' => $nama,
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => $prodi->kode_prodi,
            'NAMA_PRODI' => $prodi->nama_prodi,
            'TAHAPAN_SIDANG' => $tahapan,
            'STRATA' => $strata,
            'STATUS_AKTIF' => $status !== '' ? $status : 'AKTIF',
            'TGL_CREATE' => now(),
        ]);
    }

    private static function storePenilaian(array $v, $user, int $row, array &$return): void
    {
        [$kodeProdi, $penilaian, $noForm, $tahapan, $strata, $status, $statusCatatan, $keterangan] = $v;

        if ($user['role'] === 'TU Prodi') {
            $kodeProdi = $user['kode_prodi'];
        }

        if ($penilaian === '') {
            throw new \Exception('PENILAIAN kosong');
        }

        $prodi = TProdi::where('KODE_PRODI', $kodeProdi)->first();
        if (!$prodi) {
            throw new \Exception('Kode prodi ' . ($kodeProdi ?: '-') . ' tidak terdaftar');
        }

        TPointPenilaian::create([
            'PENILAIAN' => $penilaian,
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => $prodi->kode_prodi,
            'NAMA_PRODI' => $prodi->nama_prodi,
            'NO_FORM' => $noForm,
            'TAHAPAN_SIDANG' => $tahapan,
            'STRATA' => $strata,
            'STATUS_AKTIF' => $status !== '' ? $status : 'AKTIF',
            'STATUS_CATATAN' => $statusCatatan !== '' ? $statusCatatan : 't',
            'KETERANGAN' => $keterangan,
            'TGL_CREATE' => now(),
        ]);
    }

    private static function storeProdi(array $v, $user, int $row, array &$return): void
    {
        [$kode, $nama, $status] = $v;

        if ($nama === '') {
            throw new \Exception('NAMA PRODI kosong');
        }

        if (TProdi::where('KODE_PRODI', $kode)->exists()) {
            throw new \Exception('Kode prodi ' . ($kode ?: '-') . ' sudah terdaftar');
        }

        TProdi::create([
            'KODE_PRODI' => $kode,
            'NAMA_PRODI' => $nama,
            'STATUS_AKTIF' => $status !== '' ? $status : 'AKTIF',
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