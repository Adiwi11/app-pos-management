<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
if (basename($_SERVER['PHP_SELF']) === 'AuthController.php') {
    $action = isset($_GET['action']) ? bersihkanInput($_GET['action']) : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'login') {
            prosesLogin();
        } else {
            kirimResponseJson(false, "Permintaan tidak valid", [], 400);
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($action === 'logout') {
            prosesLogout();
        }
    }
}
function prosesLogin() {
    $email = isset($_POST['email']) ? bersihkanInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validasiCsrfToken($token)) {
        kirimResponseJson(false, "Form kadaluarsa atau token tidak valid, silakan coba lagi.", [
            'csrf_token' => buatCsrfToken()
        ], 403);
    }
    if (empty($email) || empty($password)) {
         kirimResponseJson(false, "Pastikan Email dan Password telah diisi.", [
             'csrf_token' => buatCsrfToken()
         ], 400);
    }
    $db = Database::dapatkanKoneksi();
    $sql = "SELECT p.id_pengguna, p.nama_lengkap, p.email, p.password, p.id_role, p.status, r.nama_role 
            FROM pengguna p 
            INNER JOIN role r ON p.id_role = r.id_role
            WHERE p.email = :email LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    if ($user) {
        if ($user['status'] !== 'aktif') {
            kirimResponseJson(false, "Akun Anda di-nonaktifkan, hubungi administrator.", [
                'csrf_token' => buatCsrfToken()
            ], 403);
        }
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); 
            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['id_role'] = $user['id_role'];
            $_SESSION['nama_role'] = $user['nama_role'];
            $_SESSION['waktu_login'] = time();
            $_SESSION['ip_address'] = ambilIpAddress();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            simpanHakAksesSession($db, $user['id_role']);
            catatAuditLog($db, $user['id_pengguna'], "Berhasil Login ke sistem", "pengguna");
            kirimResponseJson(true, "Login berhasil, sedang mengalihkan...", [
                'redirect' => 'dashboard.php'
            ]);
        } else {
            catatAuditLog($db, $user['id_pengguna'], "Login gagal - Password salah", "pengguna");
            kirimResponseJson(false, "Kredensial login (Email / Password) tidak sesuai.", [
                'csrf_token' => buatCsrfToken()
            ], 401);
        }
    } else {
        kirimResponseJson(false, "Kredensial login tidak ditemukan.", [
             'csrf_token' => buatCsrfToken()
        ], 401);
    }
}
function prosesLogout() {
    if (isset($_SESSION['id_pengguna'])) {
        $db = Database::dapatkanKoneksi();
        catatAuditLog($db, $_SESSION['id_pengguna'], "Logout dari sistem", "pengguna");
    }
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    arahkanHalaman('../login.php');
}
function simpanHakAksesSession($db, $idRole) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $sqlAkses = "SELECT nama_modul, akses_lihat, akses_tambah, akses_ubah, akses_hapus 
                 FROM hak_akses WHERE id_role = :id_role";
    $stmtAkses = $db->prepare($sqlAkses);
    $stmtAkses->execute(['id_role' => $idRole]);
    $listAkses = $stmtAkses->fetchAll();
    $_SESSION['hak_akses'] = [];
    foreach($listAkses as $akses) {
        $_SESSION['hak_akses'][$akses['nama_modul']] = [
            'akses_lihat'  => $akses['akses_lihat'],
            'akses_tambah' => $akses['akses_tambah'],
            'akses_ubah'   => $akses['akses_ubah'],
            'akses_hapus'  => $akses['akses_hapus'],
        ];
    }
}
function catatAuditLog($db, $idPengguna, $aksi, $tabelTerkait) {
    $sql = "INSERT INTO audit_log (id_pengguna, aksi, tabel_terkait, ip_address) 
            VALUES (:id_pengguna, :aksi, :tabel_terkait, :ip_address)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_pengguna' => $idPengguna,
        'aksi' => $aksi,
        'tabel_terkait' => $tabelTerkait,
        'ip_address' => ambilIpAddress()
    ]);
}
