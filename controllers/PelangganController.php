<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
cekBelumLogin();
$action = isset($_GET['action']) ? bersihkanInput($_GET['action']) : '';
$db = Database::dapatkanKoneksi();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'list_data') {
        $stmt = $db->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = [];
        foreach ($data as $row) {
            $btn = '<div class="btn-group">';
            $btn .= '<button class="btn btn-sm btn-light text-primary border rounded-start btn-edit" data-id="'.$row['id_pelanggan'].'" data-nama="'.htmlspecialchars($row['nama_pelanggan'],ENT_QUOTES).'" data-telp="'.htmlspecialchars($row['no_telp'],ENT_QUOTES).'" data-alamat="'.htmlspecialchars($row['alamat'],ENT_QUOTES).'"><i class="bi bi-pencil-square"></i></button>';
            $btn .= '<button class="btn btn-sm btn-light text-danger border rounded-end btn-hapus" data-id="'.$row['id_pelanggan'].'"><i class="bi bi-trash"></i></button>';
            $btn .= '</div>';
            $response[] = [ htmlspecialchars($row['nama_pelanggan']), htmlspecialchars($row['no_telp']), htmlspecialchars($row['alamat']), $btn ];
        }
        echo json_encode(["data" => $response]); exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validasiCsrfToken($_POST['csrf_token'] ?? '')) kirimResponseJson(false, "Keamanan form tidak valid.");
    if ($action === 'simpan') {
        $id = (int)($_POST['id_pelanggan'] ?? 0);
        $nama = bersihkanInput($_POST['nama_pelanggan'] ?? '');
        $telp = bersihkanInput($_POST['no_telp'] ?? '');
        $alamat = bersihkanInput($_POST['alamat'] ?? '');
        if(empty($nama)) kirimResponseJson(false, "Nama Pelanggan wajib diisi.");
        if($id > 0) {
            $st = $db->prepare("UPDATE pelanggan SET nama_pelanggan=?, no_telp=?, alamat=? WHERE id_pelanggan=?");
            $st->execute([$nama, $telp, $alamat, $id]);
            kirimResponseJson(true, "Data Pelanggan dimutahirkan.");
        } else {
            $st = $db->prepare("INSERT INTO pelanggan (nama_pelanggan, no_telp, alamat) VALUES (?,?,?)");
            $st->execute([$nama, $telp, $alamat]);
            kirimResponseJson(true, "Data Pelanggan sukses didaftarkan.");
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id_pelanggan'] ?? 0);
        if($id > 0) { $st = $db->prepare("DELETE FROM pelanggan WHERE id_pelanggan=?"); $st->execute([$id]); kirimResponseJson(true, "Terhapus total."); }
    }
}
