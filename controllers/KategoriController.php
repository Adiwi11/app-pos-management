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
    if ($action === 'list_data') {
        getListKategori($db);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validasiCsrfToken($token)) {
        kirimResponseJson(false, "Keamanan CSRF tidak valid. Silakan mutahirkan halaman (F5).", [], 403);
    }
    if ($action === 'simpan') {
        simpanKategori($db);
    } elseif ($action === 'hapus') {
        hapusKategori($db);
    }
}
function getListKategori($db) {
    $stmt = $db->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY id_kategori DESC");
    $data = $stmt->fetchAll();
    $response = [];
    $no = 1;
    foreach ($data as $row) {
        $btnAksi = '<div class="btn-group">';
        $btnAksi .= '<button class="btn btn-sm btn-light text-primary border rounded-start btn-edit" data-id="'.$row['id_kategori'].'" data-nama="'.htmlspecialchars($row['nama_kategori'], ENT_QUOTES).'" title="Ubah Info Kategori"><i class="bi bi-pencil-square"></i></button>';
        $btnAksi .= '<button class="btn btn-sm btn-light text-danger border rounded-end btn-hapus ms-1" data-id="'.$row['id_kategori'].'" title="Hapus Kategori"><i class="bi bi-trash"></i></button>';
        $btnAksi .= '</div>';
        $response[] = [
            '<span class="ps-3">'.$no++.'</span>',
            htmlspecialchars($row['nama_kategori']),
            $btnAksi
        ];
    }
    echo json_encode(["data" => $response]);
    exit;
}
function simpanKategori($db) {
    $id_kategori = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 0;
    $nama_kategori = bersihkanInput($_POST['nama_kategori'] ?? '');
    if(empty($nama_kategori)) {
        kirimResponseJson(false, "Nama kategori wajib diberikan.");
    }
    try {
        if ($id_kategori > 0) {
            $stmtCek = $db->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = :nama AND id_kategori != :id");
            $stmtCek->execute(['nama' => $nama_kategori, 'id' => $id_kategori]);
            if($stmtCek->rowCount() > 0) { kirimResponseJson(false, "Kategori dengan nama serupa telah eksis."); }
            $stmt = $db->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
            $stmt->execute([$nama_kategori, $id_kategori]);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Mengubah kategori id $id_kategori", "kategori");
            kirimResponseJson(true, "Kategori sukses diperbarui.");
        } else {
            $stmtCek = $db->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = :nama");
            $stmtCek->execute(['nama' => $nama_kategori]);
            if($stmtCek->rowCount() > 0) { kirimResponseJson(false, "Nama kategori sudah diambil."); }
            $stmt = $db->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
            $stmt->execute([$nama_kategori]);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Menambah record baru ($nama_kategori)", "kategori");
            kirimResponseJson(true, "Kategori baru berhasil dimasukkan.");
        }
    } catch (PDOException $e) {
        kirimResponseJson(false, "Error server database.");
    }
}
function hapusKategori($db) {
    $id_kategori = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 0;
    try {
        if($id_kategori > 0) {
            $stmt = $db->prepare("DELETE FROM kategori WHERE id_kategori = ?");
            $stmt->execute([$id_kategori]);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Menghapus Kategori ($id_kategori)", "kategori");
            kirimResponseJson(true, "Kategori dilenyapkan.");
        }
        kirimResponseJson(false, "ID Tidak Valid!");
    } catch (PDOException $e) {
        kirimResponseJson(false, "Galat! Kategori ini barangkali terkait dengan banyak data produk.");
    }
}
