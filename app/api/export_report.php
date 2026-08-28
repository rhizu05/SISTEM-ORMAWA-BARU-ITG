<?php
/**
 * File: export_report.php
 * Ekspor data statistik ke Excel atau PDF
 */

require_once dirname(__DIR__, 2) . '/config.php';
require_once ROOT_PATH . '/vendor/autoload.php';

check_login();
$role = $_SESSION['user_role'];
if (!in_array($role, ['bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin'])) {
    http_response_code(403);
    die("Akses Ditolak.");
}

$format = $_GET['format'] ?? 'excel'; // 'excel' or 'pdf'
$type = $_GET['type'] ?? 'top_ormawa'; 

// Catat aktivitas ekspor
log_audit($conn, 'EXPORT_REPORT', 'report', $type, ['format' => $format]);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

// --- AMBIL DATA ---
$data = [];
$title = "Laporan Sistem Kemahasiswaan";

if ($type == 'top_ormawa') {
    $title = "Laporan Pencairan Dana Top Ormawa";
    $res = $conn->query("SELECT u.nama_lengkap as ormawa, SUM(p.nominal_pengajuan) as total, COUNT(p.id_pengajuan) as jumlah_kegiatan 
                         FROM pengajuan p JOIN users u ON p.id_user_ormawa = u.id_user 
                         WHERE p.status = 'Selesai' OR p.status = 'Dana Cair'
                         GROUP BY p.id_user_ormawa ORDER BY total DESC");
    $headers = ['Nama Ormawa', 'Total Dana Cair (Rp)', 'Jumlah Kegiatan'];
    while ($row = $res->fetch_assoc()) {
        $data[] = [
            $row['ormawa'],
            "Rp " . number_format($row['total'], 0, ',', '.'),
            $row['jumlah_kegiatan']
        ];
    }
}

// --- EXPORT PDF ---
if ($format === 'pdf') {
    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $dompdf = new Dompdf($options);
    
    $html = "<html><head><style>
                body { font-family: sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
             </style></head><body>";
    $html .= "<h2>{$title}</h2>";
    $html .= "<p>Dicetak pada: " . date('d M Y H:i:s') . "</p>";
    $html .= "<table><thead><tr>";
    foreach ($headers as $h) { $html .= "<th>{$h}</th>"; }
    $html .= "</tr></thead><tbody>";
    
    foreach ($data as $row) {
        $html .= "<tr>";
        foreach ($row as $cell) { $html .= "<td>{$cell}</td>"; }
        $html .= "</tr>";
    }
    
    $html .= "</tbody></table></body></html>";
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream(strtolower(str_replace(' ', '_', $title)) . ".pdf", ["Attachment" => true]);
    exit;
} 
// --- EXPORT EXCEL ---
else {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set Title
    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells('A1:C1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    
    // Set Headers
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col . '3', $h);
        $sheet->getStyle($col . '3')->getFont()->setBold(true);
        $col++;
    }
    
    // Set Data
    $rowNum = 4;
    foreach ($data as $row) {
        $col = 'A';
        foreach ($row as $cell) {
            $sheet->setCellValue($col . $rowNum, $cell);
            $col++;
        }
        $rowNum++;
    }
    
    // Auto size columns
    foreach (range('A', $col) as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Export
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ', '_', $title)).'.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
