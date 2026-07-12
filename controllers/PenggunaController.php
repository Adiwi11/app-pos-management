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
        getListPengguna($db);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validasiCsrfToken($token)) {
        kirimResponseJson(false, "Keamanan CSRF tidak valid. Silakan muat ulang.", [], 403);
    }
    if ($action === 'simpan') {
        simpanPengguna($db);
    } elseif ($action === 'update_profil') {
        updateProfilPribadi($db);
    } elseif ($action === 'hapus') {
        hapusPengguna($db);
    } else {
        kirimResponseJson(false, "Aksi tidak dikenali.", [], 400);
    }
}
function getListPengguna($db) {
    $sql = "SELECT p.id_pengguna, p.nama_lengkap, p.email, p.status, p.id_role, r.nama_role 
            FROM pengguna p 
            INNER JOIN role r ON p.id_role = r.id_role
            ORDER BY p.id_pengguna DESC";
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = [];
    $no = 1;
    $bisaUbah = isset($_SESSION['hak_akses']['pengguna']['akses_ubah']) && $_SESSION['hak_akses']['pengguna']['akses_ubah'] == 1;
    $bisaHapus = isset($_SESSION['hak_akses']['pengguna']['akses_hapus']) && $_SESSION['hak_akses']['pengguna']['akses_hapus'] == 1;
    foreach ($data as $row) {
        $badgeStatus = ($row['status'] == 'aktif') 
            ? '<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-person-check me-1"></i>Aktif</span>'
            : '<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill"><i class="bi bi-person-x me-1"></i>Nonaktif</span>';
        $btnAksi = '<div class="btn-group">';
        $btnAksi .= '<button class="btn btn-sm btn-outline-primary btn-edit" data-id="'.$row['id_pengguna'].'" data-nama="'.htmlspecialchars($row['nama_lengkap'], ENT_QUOTES).'" data-email="'.htmlspecialchars($row['email'], ENT_QUOTES).'" data-role="'.$row['id_role'].'" data-status="'.$row['status'].'" title="Edit"><i class="bi bi-pencil-square"></i></button>';
        if($row['id_pengguna'] != $_SESSION['id_pengguna']) {
            $btnAksi .= '<button class="btn btn-sm btn-outline-danger btn-hapus" data-id="'.$row['id_pengguna'].'" title="Hapus"><i class="bi bi-trash"></i></button>';
        } else {
             $btnAksi .= '<button class="btn btn-sm btn-outline-secondary disabled" title="Anda tidak bisa menghapus diri sendiri"><i class="bi bi-trash"></i></button>';
        }
        $btnAksi .= '</div>';
        $response[] = [
            $no++,
            htmlspecialchars($row['nama_lengkap']),
            htmlspecialchars($row['email']),
            htmlspecialchars($row['nama_role']),
            $badgeStatus,
            $btnAksi
        ];
    }
    echo json_encode([
        "data" => $response
    ]);
    exit;
}
function simpanPengguna($db) {
    $id_pengguna = isset($_POST['id_pengguna']) ? (int)$_POST['id_pengguna'] : 0;
    $nama_lengkap = bersihkanInput($_POST['nama_lengkap'] ?? '');
    $email = bersihkanInput($_POST['email'] ?? '');
    $id_role = (int)($_POST['id_role'] ?? 0);
    $status = in_array($_POST['status'] ?? '', ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';
    $password = $_POST['password'] ?? '';
    if (empty($nama_lengkap) || empty($email) || empty($id_role)) {
        kirimResponseJson(false, "Wajib melengkapi form secara utuh.");
    }
    try {
        if ($id_pengguna > 0) {
            $sqlCek = "SELECT id_pengguna FROM pengguna WHERE email = :email AND id_pengguna != :id";
            $stmtCek = $db->prepare($sqlCek);
            $stmtCek->execute(['email' => $email, 'id' => $id_pengguna]);
            if ($stmtCek->rowCount() > 0) {
                 kirimResponseJson(false, "Email sudah digunakan oleh pengguna lain.");
            }
            if (!empty($password)) { 
                $hashPassword = password_hash($password, PASSWORD_BCRYPT);
                $sqlup = "UPDATE pengguna SET nama_lengkap = ?, email = ?, password = ?, id_role = ?, status = ? WHERE id_pengguna = ?";
                $stmt = $db->prepare($sqlup);
                $stmt->execute([$nama_lengkap, $email, $hashPassword, $id_role, $status, $id_pengguna]);
            } else { 
                $sqlup = "UPDATE pengguna SET nama_lengkap = ?, email = ?, id_role = ?, status = ? WHERE id_pengguna = ?";
                $stmt = $db->prepare($sqlup);
                $stmt->execute([$nama_lengkap, $email, $id_role, $status, $id_pengguna]);
            }
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Mengubah data pengguna ($email)", "pengguna");
            kirimResponseJson(true, "Data pengguna berhasil diperbarui.");
        } else {
            if (empty($password)) {
                kirimResponseJson(false, "Password wajib diisi untuk pengguna baru!");
            }
            $sqlCek = "SELECT id_pengguna FROM pengguna WHERE email = :email";
            $stmtCek = $db->prepare($sqlCek);
            $stmtCek->execute(['email' => $email]);
            if ($stmtCek->rowCount() > 0) {
                 kirimResponseJson(false, "Email sudah terdaftar di sistem.");
            }
            $hashPassword = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO pengguna (nama_lengkap, email, password, id_role, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nama_lengkap, $email, $hashPassword, $id_role, $status]);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Menambah pengguna baru ($email)", "pengguna");
            kirimResponseJson(true, "Pengguna berhasil ditambahkan ke sistem.");
        }
    } catch (PDOException $e) {
         kirimResponseJson(false, "Terjadi gangguan server internal saat memproses instruksi.");
    }
}
function hapusPengguna($db) {
    $id_pengguna = isset($_POST['id_pengguna']) ? (int)$_POST['id_pengguna'] : 0;
    if ($id_pengguna == $_SESSION['id_pengguna']) {
        kirimResponseJson(false, "Anda tidak dapat menghapus akun Anda sendiri.");
    }
    if ($id_pengguna > 0) {
        $stmt = $db->prepare("DELETE FROM pengguna WHERE id_pengguna = :id");
        $stmt->execute(['id' => $id_pengguna]);
        require_once '../controllers/AuthController.php';
        catatAuditLog($db, $_SESSION['id_pengguna'], "Menghapus ID $id_pengguna dari pengguna", "pengguna");
        kirimResponseJson(true, "Akun dan rekam data berhasil dilenyapkan.");
    }
    kirimResponseJson(false, "ID Pengguna tidak diketahui.");
}
function updateProfilPribadi($db) {
    if (!isset($_SESSION['id_pengguna'])) kirimResponseJson(false, "Sesi Invalid", [], 403);
    $id = $_SESSION['id_pengguna'];
    $nama = bersihkanInput($_POST['nama_lengkap'] ?? '');
    $pass = $_POST['password'] ?? '';
    try {
        if(empty($pass)) {
            $st = $db->prepare("UPDATE pengguna SET nama_lengkap = ? WHERE id_pengguna = ?");
            $st->execute([$nama, $id]);
        } else {
            $hpass = password_hash($pass, PASSWORD_BCRYPT);
            $st = $db->prepare("UPDATE pengguna SET nama_lengkap = ?, password = ? WHERE id_pengguna = ?");
            $st->execute([$nama, $hpass, $id]);
        }
        $_SESSION['nama_lengkap'] = $nama;
        require_once '../controllers/AuthController.php';
        catatAuditLog($db, $id, "Mengganti profil/sandi akun mandiri.", "pengguna");
        kirimResponseJson(true, "Data personal telah disesuaikan!");
    } catch(PDOException $e) {
        kirimResponseJson(false, "Gagal mengikat basis data: " . $e->getMessage());
    }
}
