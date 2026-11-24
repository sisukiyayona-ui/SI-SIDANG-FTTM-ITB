<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\TUser;
use App\Models\VReportTipeI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = session('auth_user');

        $query = VReportTipeI::query();
        $this->applyRoleFilter($query, $user);

        // Filter strata: '' = Semua (S1/S2/S3 bercampur), atau S1 / S2 / S3.
        $strata = strtoupper(trim((string) $request->input('strata', '')));
        if (in_array($strata, ['S1', 'S2', 'S3'], true)) {
            $query->where('strata', $strata);
        } else {
            $strata = '';
        }

        $reports = $query->paginate($this->resolvePerPage($request, 25))->withQueryString();

        return view('report.index', compact('reports', 'strata'));
    }

    /**
     * Filter report by role (TU Prodi via t_user_prodi, Monev via FS).
     */
    private function applyRoleFilter($query, $user)
    {
        if (($user['role'] ?? '') === 'TU Prodi') {
            $kodeList = $this->prodiKodeByUserProdi($user);
            if (!empty($kodeList)) {
                $query->whereIn('kode_prodi', $kodeList);
            } elseif (!empty($user['kode_prodi'])) {
                $query->where('kode_prodi', $user['kode_prodi']);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif (($user['role'] ?? '') === 'Monev') {
            $query->whereIn('kode_prodi', $this->prodiKodeByFs($user['kode_fs'] ?? null));
        }
    }

    /**
     * Kode prodi dari t_user_prodi untuk user TU Prodi (bisa multi-prodi).
     */
    private function prodiKodeByUserProdi($user)
    {
        $prodiIds = $user['id_prodi'] ?? [];
        if (empty($prodiIds)) {
            return [];
        }

        return \App\Models\TProdi::whereIn('ID', $prodiIds)->pluck('KODE_PRODI')->all();
    }

    /**
     * Daftar kode prodi dalam satu fakultas (untuk scoping Monev).
     */
    private function prodiKodeByFs($kodeFs)
    {
        if (empty($kodeFs)) {
            return ['-1'];
        }

        return \App\Models\TProdi::where('KODE_FS', $kodeFs)->pluck('KODE_PRODI')->all();
    }
    
    public function export(Request $request)
    {
        $user = session('auth_user');

        // Export seluruh data (bukan hanya halaman DataTables saat ini),
        // mengikuti filter strata yang sedang dipilih di halaman.
        $query = VReportTipeI::query();
        $this->applyRoleFilter($query, $user);

        $strata = strtoupper(trim((string) $request->input('strata', '')));
        if (in_array($strata, ['S1', 'S2', 'S3'], true)) {
            $query->where('strata', $strata);
        }

        $reports = $query->get();
        
        // Helper function to format tahapan
        $formatTahapan = function($tahapan) {
            $tahapanLower = strtolower($tahapan ?? '');
            $labels = [
                'tahap i'   => 'Ujian Kualifikasi',
                'tahap ii'  => 'Ujian Proposal',
                'tahap iii' => 'Tahap III',
                'tahap iv'  => 'Sidang Terbuka / Tertutup',
                'sk i'      => 'SK I',
                'sk ii'     => 'SK II',
                'sk iii'    => 'SK III',
                'sk iv'     => 'SK IV',
            ];
            return $labels[$tahapanLower] ?? $tahapan;
        };

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Strata');
        $sheet->setCellValue('C1', 'Tahun');
        $sheet->setCellValue('D1', 'NIM');
        $sheet->setCellValue('E1', 'Nama Mahasiswa');
        $sheet->setCellValue('F1', 'Judul');
        $sheet->setCellValue('G1', 'NIP');
        $sheet->setCellValue('H1', 'Nama Dosen');
        $sheet->setCellValue('I1', 'Status Tim Sidang');
        $sheet->setCellValue('J1', 'Tahapan');
        $sheet->setCellValue('K1', 'Tanggal Sidang');
        $sheet->setCellValue('L1', 'Status Lulus');
        $sheet->setCellValue('M1', 'Nilai');
        
        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        
        // Set data
        $row = 2;
        foreach ($reports as $idx => $item) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $item->strata ?? '-');
            $sheet->setCellValue('C' . $row, $item->tahun);
            $sheet->setCellValue('D' . $row, $item->NIM);
            $sheet->setCellValue('E' . $row, $item->nama_mahasiswa);
            $sheet->setCellValue('F' . $row, $item->JUDUL);
            $sheet->setCellValue('G' . $row, $item->NIP);
            $sheet->setCellValue('H' . $row, $item->pembimbing_penguji);
            $sheet->setCellValue('I' . $row, $item->STATUS_TIM_SIDANG);
            $sheet->setCellValue('J' . $row, $formatTahapan($item->tahapan_sidang));
            $sheet->setCellValue('K' . $row, $item->tgl_sidang ? \Carbon\Carbon::parse($item->tgl_sidang)->format('d M Y') : '-');
            $sheet->setCellValue('L' . $row, $item->status_lulus ?? 'belum diajukan');
            $sheet->setCellValue('M' . $row, $item->nilai_rata2 ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:M' . ($row - 1))->applyFromArray($styleArray);
        
        // Generate filename
        $suffix = in_array($strata, ['S1', 'S2', 'S3'], true) ? '_' . $strata : '_Semua';
        $filename = 'Report_Sidang' . $suffix . '_' . date('Y-m-d_His') . '.xlsx';
        
        // Create writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    public function showDetail($idJudul, $tahapan)
    {
        $user = session('auth_user');

        $tahapan = str_replace('_', ' ', $tahapan);

        $judul = DB::table('t_judul')->where('id', $idJudul)->first();
        
        if (!$judul) {
            return response()->json(['error' => 'Judul tidak ditemukan'], 404);
        }
        
        $detailQuery = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->with(['timSidang', 'cekPersyaratan']);

        $strata = strtoupper(trim((string) request()->input('strata', '')));
        if (in_array($strata, ['S1', 'S2', 'S3'], true)) {
            $detailQuery->where('Strata', $strata);
        }
        $detail = $detailQuery->first();
        
        if (!$detail) {
            return response()->json([
                'Judul' => $judul->JUDUL,
                'tahapan_sidang' => $tahapan,
                'tgl_sidang' => null,
                'waktu_sidang' => null,
                'ruang_sidang' => null,
                'status_lulus' => 'belum diajukan',
            ]);
        }
        
        if (in_array($user['role'], ['TU Prodi', 'FS'])) {
            // Filter by prodi/FS if needed
        }
        
        return response()->json($detail);
    }
}
