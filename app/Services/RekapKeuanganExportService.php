<?php

namespace App\Services;

use App\Models\Dana;
use App\Models\Konfigurasi;
use App\Models\Pengajuan;
use App\Models\PeriodeAnggaran;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapKeuanganExportService
{
    /**
     * Mengambil data rekapitulasi transaksi keuangan, proposal, dan pencairan.
     */
    public static function getData(?int $periodeId = null): array
    {
        $periode = $periodeId ? PeriodeAnggaran::find($periodeId) : PeriodeAnggaran::where('is_aktif', true)->first();

        // Ambil data pencairan (Dana) beserta relasi pengajuan & user
        $danas = Dana::with(['pengajuan.user', 'pengajuan.state'])
            ->latest()
            ->get();

        $rows = [];

        foreach ($danas as $dana) {
            $pengajuan = $dana->pengajuan;
            if (!$pengajuan) {
                continue;
            }

            $user = $pengajuan->user;
            $statusLpj = 'Belum Upload';
            if ($pengajuan->file_lpj) {
                $statusLpj = ($pengajuan->state && $pengajuan->state->name === 'completed') ? 'Diverifikasi' : 'Diajukan';
            }

            $rows[] = [
                'ormawa' => $user->name ?? '-',
                'judul_kegiatan' => $pengajuan->nama_kegiatan,
                'tgl_pengajuan' => $pengajuan->tanggal_pengajuan ? \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') : '-',
                'dana_diajukan' => (float) $pengajuan->dana_diajukan,
                'dana_disetujui' => (float) $dana->nominal_cair,
                'termin' => 'Termin ' . ($dana->termin_ke ?? 1),
                'tgl_pencairan' => $dana->created_at ? $dana->created_at->format('d/m/Y') : '-',
                'status_lpj' => $statusLpj,
                'sisa_saldo_pagu' => (float) ($user->saldo ?? 0),
            ];
        }

        // Jika belum ada pencairan tapi ada pengajuan yang disetujui
        if (empty($rows)) {
            $pengajuans = Pengajuan::with(['user', 'state'])
                ->whereHas('state', fn($q) => $q->whereNotIn('name', ['draft', 'rejected']))
                ->latest()
                ->get();

            foreach ($pengajuans as $pengajuan) {
                $user = $pengajuan->user;
                $rows[] = [
                    'ormawa' => $user->name ?? '-',
                    'judul_kegiatan' => $pengajuan->nama_kegiatan,
                    'tgl_pengajuan' => $pengajuan->tanggal_pengajuan ? \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') : '-',
                    'dana_diajukan' => (float) $pengajuan->dana_diajukan,
                    'dana_disetujui' => (float) ($pengajuan->dana_disetujui ?? 0),
                    'termin' => 'Belum Cair',
                    'tgl_pencairan' => '-',
                    'status_lpj' => $pengajuan->file_lpj ? 'Diajukan' : 'Belum Upload',
                    'sisa_saldo_pagu' => (float) ($user->saldo ?? 0),
                ];
            }
        }

        return [
            'periodeNama' => $periode?->nama_periode ?? 'Tahun Anggaran Berjalan',
            'items' => $rows,
        ];
    }

    /**
     * Ekspor rekapitulasi data keuangan ke format Excel resmi (.xlsx).
     */
    public static function exportExcel(?int $periodeId = null): StreamedResponse
    {
        $data = self::getData($periodeId);
        $items = $data['items'];
        $periodeNama = $data['periodeNama'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Keuangan');

        // Header Dokumen
        $sheet->setCellValue('A1', 'INSTITUT TEKNOLOGI GARUT');
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI REALISASI ANGGARAN & PENCAIRAN KEGIATAN MAHASISWA');
        $sheet->setCellValue('A3', 'Periode: ' . $periodeNama . ' | Tanggal Unduh: ' . date('d/m/Y H:i') . ' WIB');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(12);

        // Header Tabel
        $headers = [
            'A5' => 'No',
            'B5' => 'Nama Ormawa',
            'C5' => 'Judul Kegiatan',
            'D5' => 'Tgl Pengajuan',
            'E5' => 'Nominal Diajukan (Rp)',
            'F5' => 'Nominal Disetujui (Rp)',
            'G5' => 'Termin',
            'H5' => 'Tgl Pencairan',
            'I5' => 'Status LPJ',
            'J5' => 'Sisa Pagu Ormawa (Rp)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy Blue ITG
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Data Rows
        $rowIdx = 6;
        $totalDiajukan = 0;
        $totalDisetujui = 0;

        foreach ($items as $no => $item) {
            $totalDiajukan += $item['dana_diajukan'];
            $totalDisetujui += $item['dana_disetujui'];

            $sheet->setCellValue('A' . $rowIdx, $no + 1);
            $sheet->setCellValue('B' . $rowIdx, $item['ormawa']);
            $sheet->setCellValue('C' . $rowIdx, $item['judul_kegiatan']);
            $sheet->setCellValue('D' . $rowIdx, $item['tgl_pengajuan']);
            $sheet->setCellValue('E' . $rowIdx, $item['dana_diajukan']);
            $sheet->setCellValue('F' . $rowIdx, $item['dana_disetujui']);
            $sheet->setCellValue('G' . $rowIdx, $item['termin']);
            $sheet->setCellValue('H' . $rowIdx, $item['tgl_pencairan']);
            $sheet->setCellValue('I' . $rowIdx, $item['status_lpj']);
            $sheet->setCellValue('J' . $rowIdx, $item['sisa_saldo_pagu']);

            // Format angka nominal
            $sheet->getStyle('E' . $rowIdx . ':F' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');

            // Alignment
            $sheet->getStyle('A' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $rowIdx . ':I' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowIdx . ':F' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $rowIdx++;
        }

        // Total Row
        if (count($items) > 0) {
            $sheet->setCellValue('A' . $rowIdx, 'TOTAL');
            $sheet->mergeCells('A' . $rowIdx . ':D' . $rowIdx);
            $sheet->setCellValue('E' . $rowIdx, $totalDiajukan);
            $sheet->setCellValue('F' . $rowIdx, $totalDisetujui);

            $sheet->getStyle('A' . $rowIdx . ':J' . $rowIdx)->getFont()->setBold(true);
            $sheet->getStyle('A' . $rowIdx . ':D' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $rowIdx . ':F' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $rowIdx . ':J' . $rowIdx)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        }

        // Apply borders to all data cells
        $sheet->getStyle('A5:J' . $rowIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'rekap_keuangan_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor rekapitulasi data keuangan ke format PDF resmi.
     */
    public static function exportPdf(?int $periodeId = null)
    {
        $data = self::getData($periodeId);
        $konfig = Konfigurasi::first();

        $pdf = Pdf::loadView('laporan.rekap_keuangan_pdf', [
            'konfig' => $konfig,
            'items' => $data['items'],
            'periodeNama' => $data['periodeNama'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekap_keuangan_' . date('Ymd_His') . '.pdf');
    }
}
