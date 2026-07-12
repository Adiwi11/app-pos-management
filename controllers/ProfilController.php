<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
cekBelumLogin();
$action = isset($_GET['action']) ? bersihkanInput($_GET['action']) : '';
$db = Database::dapatkanKoneksi();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    if (!validasiCsrfToken($_POST['csrf_token'] ?? '')) kirimResponseJson(false, "Keamanan diTolak!");
    $nm = bersihkanInput($_POST['nama_toko']);
    $tp = bersihkanInput($_POST['telepon']);
    $al = bersihkanInput($_POST['alamat']);
    if(empty($nm)) kirimResponseJson(false, "Nama toko tidak diizinkan Null/Kosong.");
    try {
        $ck = $db->query("SELECT id_konfig FROM konfigurasi LIMIT 1")->fetchColumn();
        if($ck) {
            $db->prepare("UPDATE konfigurasi SET nama_toko=?, alamat=?, telepon=? WHERE id_konfig=?")->execute([$nm, $al, $tp, $ck]);
        } else {
            $db->prepare("INSERT INTO konfigurasi (nama_toko, alamat, telepon) VALUES (?,?,?)")->execute([$nm, $al, $tp]);
        }
        require_once '../controllers/AuthController.php';
        catatAuditLog($db, $_SESSION['id_pengguna'], "Merubah Profil/Identitas Toko", "konfigurasi");
        kirimResponseJson(true, "Setup Identitas Outlet Sukses di-update.");
    } catch(Exception $e) { kirimResponseJson(false, "DB Error ".$e->getMessage()); }
}
