<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\TUser;
use App\Models\VReportTipeI;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = session('auth_user');

        $query = VReportTipeI::query();

        if ($user['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $user['kode_prodi']);
        } elseif (in_array($user['role'], ['FS', 'Monev'])) {
            // sees all records
        }

        $reports = $query->get();

        return view('report.index', compact('reports'));
    }
    
    public function export()
    {
        $user = session('auth_user');

        $query = VReportTipeI::query();

        if ($user['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $user['kode_prodi']);
        } elseif (in_array($user['role'], ['FS', 'Monev'])) {
            // sees all records
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
        $sheet->setCellValue('B1', 'Tahun');
        $sheet->setCellValue('C1', 'NIM');
        $sheet->setCellValue('D1', 'Nama Mahasiswa');
        $sheet->setCellValue('E1', 'Judul');
        $sheet->setCellValue('F1', 'NIP');
        $sheet->setCellValue('G1', 'Nama Dosen');
        $sheet->setCellValue('H1', 'Status Tim Sidang');
        $sheet->setCellValue('I1', 'Tahapan');
        $sheet->setCellValue('J1', 'Tanggal Sidang');
        $sheet->setCellValue('K1', 'Status Lulus');
        $sheet->setCellValue('L1', 'Nilai');
        
        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
        
        // Set data
        $row = 2;
        foreach ($reports as $idx => $item) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $item->tahun);
            $sheet->setCellValue('C' . $row, $item->NIM);
            $sheet->setCellValue('D' . $row, $item->nama_mahasiswa);
            $sheet->setCellValue('E' . $row, $item->JUDUL);
            $sheet->setCellValue('F' . $row, $item->NIP);
            $sheet->setCellValue('G' . $row, $item->pembimbing_penguji);
            $sheet->setCellValue('H' . $row, $item->STATUS_TIM_SIDANG);
            $sheet->setCellValue('I' . $row, $formatTahapan($item->tahapan_sidang));
            $sheet->setCellValue('J' . $row, $item->tgl_sidang ? \Carbon\Carbon::parse($item->tgl_sidang)->format('d M Y') : '-');
            $sheet->setCellValue('K' . $row, $item->status_lulus ?? 'belum diajukan');
            $sheet->setCellValue('L' . $row, $item->nilai_rata2 ?? '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'L') as $col) {
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
        $sheet->getStyle('A1:K' . ($row - 1))->applyFromArray($styleArray);
        
        // Generate filename
        $filename = 'Report_Sidang_S3_' . date('Y-m-d_His') . '.xlsx';
        
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
        
        $judul = DB::table('t_judul')->where('id', $idJudul)->first();
        
        if (!$judul) {
            return response()->json(['error' => 'Judul tidak ditemukan'], 404);
        }
        
        $detail = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->where('Strata', 'S3')
            ->with(['timSidang', 'cekPersyaratan'])
            ->first();
        
        if (!$detail) {
            return response()->json([
                'Judul' => $judul->Judul,
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
