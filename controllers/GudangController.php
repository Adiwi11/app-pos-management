<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
cekBelumLogin();
$action = isset($_GET['action']) ? bersihkanInput($_GET['action']) : '';
$db = Database::dapatkanKoneksi();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list_mutasi') {
    $stmt = $db->query("
        SELECT r.*, p.nama_produk, p.sku 
        FROM riwayat_stok r
        JOIN produk p ON r.id_produk = p.id_produk
        ORDER BY r.tanggal DESC, r.id_riwayat DESC
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rsp = [];
    foreach($data as $r) {
        $dt = date('d M Y H:i', strtotime($r['tanggal']));
        $brg = "<strong class='text-dark'>{$r['nama_produk']}</strong> <div class='small text-muted'>SKU: {$r['sku']}</div>";
        $tipe = $r['jenis'];
        if($tipe === 'masuk') {
            $bdg = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-3 rounded-pill"><i class="bi bi-box-arrow-in-right"></i> Masuk</span>';
            $qty = '<span class="text-success">+ ' . $r['jumlah'] . '</span>';
        } else {
            $bdg = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-3 rounded-pill"><i class="bi bi-box-arrow-right"></i> Keluar</span>';
            $qty = '<span class="text-danger">- ' . $r['jumlah'] . '</span>';
        }
        $rsp[] = [ $dt, $brg, $bdg, $qty, htmlspecialchars($r['keterangan']) ];
    }
    echo json_encode(["data" => $rsp]); exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'opname') {
    if (!validasiCsrfToken($_POST['csrf_token'] ?? '')) kirimResponseJson(false, "Keamanan Form ditolak!");
    $id = (int)$_POST['id_produk'];
    $jenis = bersihkanInput($_POST['jenis']);
    $jum = (int)$_POST['jumlah_selisih'];
    $ket = bersihkanInput($_POST['keterangan']);
    if($jum <= 0) kirimResponseJson(false, "Selisih minimal bernilai 1. Tidak bisa 0 atau mines (-).");
    if(!in_array($jenis, ['masuk', 'keluar'])) kirimResponseJson(false, "Tipe Opname tidak dikenali.");
    try {
        $db->beginTransaction();
        $sCek = $db->prepare("SELECT stok FROM produk WHERE id_produk = ? FOR UPDATE");
        $sCek->execute([$id]);
        $old = $sCek->fetchColumn();
        if($jenis === 'keluar' && $old < $jum) {
            $db->rollBack(); kirimResponseJson(false, "Kegagalan Opname. Jumlah pengurangan ($jum) melebihi stok yang ada pada komputer ($old).");
        }
        if($jenis === 'masuk') $q = "UPDATE produk SET stok = stok + ? WHERE id_produk = ?";
        else $q = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
        $db->prepare($q)->execute([$jum, $id]);
        $logStr = "[OPNAME MANUAL] " . $ket;
        $db->prepare("INSERT INTO riwayat_stok (id_produk, jenis, jumlah, keterangan) VALUES (?,?,?,?)")->execute([$id, $jenis, $jum, $logStr]);
        require_once '../controllers/AuthController.php';
        catatAuditLog($db, $_SESSION['id_pengguna'], "Menjalankan Opname Stok pada produk_id $id", "produk & riwayat_stok");
        $db->commit();
        kirimResponseJson(true, "Tindakan Audit / Stock Opname Telah di Implementasikan.");
    } catch(Exception $e) {
        $db->rollBack(); kirimResponseJson(false, "Kesalahan Sistem Basis Data: " . $e->getMessage());
    }
}
