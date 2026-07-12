<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
cekBelumLogin();
$action = isset($_GET['action']) ? bersihkanInput($_GET['action']) : '';
$db = Database::dapatkanKoneksi();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list_audit') {
    $stmt = $db->query("
        SELECT a.*, u.nama_lengkap, u.email 
        FROM audit_log a
        LEFT JOIN pengguna u ON a.id_pengguna = u.id_pengguna
        ORDER BY a.waktu DESC, a.id_log DESC
        LIMIT 1000 -- Hard limit for application server stability if big data
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rsp = [];
    foreach($data as $r) {
        $dt = date('d-M-Y H:i:s', strtotime($r['waktu']));
        $usr = ($r['nama_lengkap'] ?: 'N/A') . "<br><span class='small text-muted fw-normal'>".$r['email']."</span>";
        $ak = "<span class='text-dark'>".htmlspecialchars($r['aksi'])."</span>";
        $tbl = strtoupper(htmlspecialchars($r['tabel_terkait'] ?? 'Sistem Umum'));
        $ip = "<code class='bg-light text-secondary px-2 border rounded'>" . htmlspecialchars($r['ip_address'] ?? '0.0.0.0') . "</code>";
        $rsp[] = [ $dt, $usr, $ak, $tbl, $ip ];
    }
    echo json_encode(["data" => $rsp]); exit;
}
