<?php
/**
 * File: api_dashboard.php
 * Endpoint API untuk mendapatkan statistik dashboard (Chart.js)
 */
require_once dirname(__DIR__, 2) . '/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// Utility function to get 12 months array (from current month backwards)
function getLast12Months() {
    $months = [];
    for ($i = 11; $i >= 0; $i--) {
        $months[] = date('Y-m', strtotime("-$i months"));
    }
    return $months;
}

$response = [];

try {
    // 1. STATS UMUM BERDASARKAN ROLE
    if ($role === 'ormawa') {
        // Status Pengajuan (Doughnut Chart)
        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM pengajuan WHERE id_user_ormawa = ? GROUP BY status");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $status_data = [];
        while ($row = $res->fetch_assoc()) {
            $status_data[$row['status']] = $row['count'];
        }
        $response['status_pengajuan'] = $status_data;

        // Tren Realisasi Pencairan per Bulan (Bar Chart)
        $months = getLast12Months();
        $realisasi_data = array_fill_keys($months, 0);
        
        $stmt = $conn->prepare("SELECT DATE_FORMAT(d.tanggal_cair, '%Y-%m') as bulan, SUM(d.nominal_cair) as total 
                               FROM dana d 
                               JOIN pengajuan p ON d.id_pengajuan = p.id_pengajuan 
                               WHERE p.id_user_ormawa = ? AND d.tanggal_cair >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                               GROUP BY bulan");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            if (isset($realisasi_data[$row['bulan']])) {
                $realisasi_data[$row['bulan']] = (float)$row['total'];
            }
        }
        $response['trend_pencairan'] = [
            'labels' => array_keys($realisasi_data),
            'data' => array_values($realisasi_data)
        ];

    } elseif (in_array($role, ['bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin'])) {
        // Status Semua Pengajuan (Pie Chart)
        $res = $conn->query("SELECT status, COUNT(*) as count FROM pengajuan GROUP BY status");
        $status_data = [];
        while ($row = $res->fetch_assoc()) {
            $status_data[$row['status']] = $row['count'];
        }
        $response['status_pengajuan_global'] = $status_data;

        // Top 5 Ormawa berdasarkan Pengajuan Disetujui (Horizontal Bar Chart)
        $res = $conn->query("SELECT u.nama_lengkap, SUM(p.nominal_pengajuan) as total 
                             FROM pengajuan p 
                             JOIN users u ON p.id_user_ormawa = u.id_user 
                             WHERE p.status = 'Selesai' OR p.status = 'Dana Cair'
                             GROUP BY p.id_user_ormawa 
                             ORDER BY total DESC LIMIT 5");
        $top_ormawa = ['labels' => [], 'data' => []];
        while ($row = $res->fetch_assoc()) {
            $top_ormawa['labels'][] = $row['nama_lengkap'];
            $top_ormawa['data'][] = (float)$row['total'];
        }
        $response['top_ormawa'] = $top_ormawa;

        // Trend Pengajuan Masuk per Bulan (Line Chart)
        $months = getLast12Months();
        $pengajuan_masuk = array_fill_keys($months, 0);
        $res = $conn->query("SELECT DATE_FORMAT(tanggal_pengajuan, '%Y-%m') as bulan, COUNT(*) as total 
                             FROM pengajuan 
                             WHERE tanggal_pengajuan >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                             GROUP BY bulan");
        while ($row = $res->fetch_assoc()) {
            if (isset($pengajuan_masuk[$row['bulan']])) {
                $pengajuan_masuk[$row['bulan']] = (int)$row['total'];
            }
        }
        $response['trend_pengajuan'] = [
            'labels' => array_keys($pengajuan_masuk),
            'data' => array_values($pengajuan_masuk)
        ];
    }

    echo json_encode(['success' => true, 'data' => $response]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
