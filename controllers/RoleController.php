<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';
require_once '../middleware/rbac_middleware.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
cekBelumLogin();
$action = isset($_GET['action']) ? bersihkanInput($_GET['action']) : '';
$db = Database::dapatkanKoneksi();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'get_matriks') {
        $id_role = isset($_GET['id_role']) ? (int)$_GET['id_role'] : 0;
        $stmt = $db->prepare("SELECT nama_modul, akses_lihat, akses_tambah, akses_ubah, akses_hapus FROM hak_akses WHERE id_role = :id");
        $stmt->execute(['id' => $id_role]);
        $data = $stmt->fetchAll();
        kirimResponseJson(true, "Data loaded", $data);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validasiCsrfToken($token)) {
        kirimResponseJson(false, "Keamanan form tidak valid (CSRF).", [], 403);
    }
    if ($action === 'tambah_role') {
        $nama_role = bersihkanInput($_POST['nama_role'] ?? '');
        if(empty($nama_role)) kirimResponseJson(false, "Nama role tidak boleh kosong");
        $stmtCek = $db->prepare("SELECT id_role FROM role WHERE nama_role = ?");
        $stmtCek->execute([$nama_role]);
        if($stmtCek->rowCount() > 0) kirimResponseJson(false, "Nama peran ini sudah ada.");
        $stmt = $db->prepare("INSERT INTO role (nama_role) VALUES (?)");
        $stmt->execute([$nama_role]);
        require_once '../controllers/AuthController.php';
        catatAuditLog($db, $_SESSION['id_pengguna'], "Menambahkan role baru ($nama_role)", "role");
        kirimResponseJson(true, "Role $nama_role terdaftar.");
    } elseif ($action === 'simpan_matriks') {
        $id_role_target = isset($_POST['id_role_target']) ? (int)$_POST['id_role_target'] : 0;
        if($id_role_target <= 0) kirimResponseJson(false, "ID Peran tidak dikenal.");
        if($id_role_target === 1) {
        }
        $listModul = $_POST['modul'] ?? [];
        try {
            $db->beginTransaction();
            $stmtHapus = $db->prepare("DELETE FROM hak_akses WHERE id_role = ?");
            $stmtHapus->execute([$id_role_target]);
            $sqlIns = "INSERT INTO hak_akses (id_role, nama_modul, akses_lihat, akses_tambah, akses_ubah, akses_hapus) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            $stmtIns = $db->prepare($sqlIns);
            foreach($listModul as $key => $val) {
                $lihat  = isset($val['lihat'])  ? 1 : 0;
                $tambah = isset($val['tambah']) ? 1 : 0;
                $ubah   = isset($val['ubah'])   ? 1 : 0;
                $hapus  = isset($val['hapus'])  ? 1 : 0;
                $stmtIns->execute([$id_role_target, $key, $lihat, $tambah, $ubah, $hapus]);
            }
            $db->commit();
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Update Matriks Modul Role (ID $id_role_target)", "hak_akses");
            if ($_SESSION['id_role'] == $id_role_target) {
                simpanHakAksesSession($db, $id_role_target);
            }
            kirimResponseJson(true, "Konfigurasi matriks berhasi disimpan.");
        } catch (PDOException $e) {
            $db->rollBack();
            kirimResponseJson(false, "Terjadi gangguan sistem: " . $e->getMessage());
        }
    }
}
