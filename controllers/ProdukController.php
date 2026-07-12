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
        getListProduk($db);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!validasiCsrfToken($token)) {
        kirimResponseJson(false, "Keamanan form tidak valid (CSRF).", [], 403);
    }
    if ($action === 'simpan') {
        simpanProduk($db);
    } elseif ($action === 'hapus') {
        hapusProduk($db);
    }
}
function getListProduk($db) {
    $sql = "SELECT p.*, k.nama_kategori 
            FROM produk p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            ORDER BY p.id_produk DESC";
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = [];
    foreach ($data as $row) {
        $gambar = empty($row['gambar']) ? '<div class="bg-light rounded text-muted d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:0.7rem;">None</div>' 
                                        : '<img src="uploads/produk/'.htmlspecialchars($row['gambar'], ENT_QUOTES).'" class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">';
        $namaKat = "<div class='fw-bold text-dark mb-1'>".htmlspecialchars($row['nama_produk'])."</div>
                    <span class='badge bg-primary bg-opacity-10 text-primary'>".htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kat')."</span>";
        $skuBarc = "<div class='text-secondary small mb-1'>SKU: " . htmlspecialchars($row['sku'] ?: '-') . "</div>
                    <div class='text-dark small'><i class='bi bi-upc-scan'></i> " . htmlspecialchars($row['barcode'] ?: '-') . "</div>";
        $harga = "<div class='text-success fw-bold'>J: Rp" . number_format($row['harga_jual'],0,',','.') . "</div>
                  <div class='text-danger small'>B: Rp" . number_format($row['harga_beli'],0,',','.') . "</div>";
        $btnAksi = '<div class="btn-group">';
        $btnAksi .= '<button class="btn btn-sm btn-light text-primary border border-end-0 rounded-start btn-edit" data-id="'.$row['id_produk'].'" data-nama="'.htmlspecialchars($row['nama_produk'], ENT_QUOTES).'" data-kategori="'.$row['id_kategori'].'" data-sku="'.htmlspecialchars($row['sku']).'" data-barcode="'.htmlspecialchars($row['barcode']).'" data-hargabeli="'.$row['harga_beli'].'" data-hargajual="'.$row['harga_jual'].'" data-deskripsi="'.htmlspecialchars($row['deskripsi'], ENT_QUOTES).'" data-gambar="'.htmlspecialchars($row['gambar']).'" title="Ubah Info"><i class="bi bi-pencil-square"></i></button>';
        $btnAksi .= '<button class="btn btn-sm btn-light text-danger border rounded-end btn-hapus" data-id="'.$row['id_produk'].'" title="Hapus"><i class="bi bi-trash"></i></button>';
        $btnAksi .= '</div>';
        $response[] = [
            $gambar,
            $namaKat,
            $skuBarc,
            "<span class='fs-5'>".$row['stok']."</span> <sub>Pcs</sub>",
            $harga,
            $btnAksi
        ];
    }
    echo json_encode(["data" => $response]);
    exit;
}
function simpanProduk($db) {
    $id_produk = isset($_POST['id_produk']) ? (int)$_POST['id_produk'] : 0;
    $nama_produk = bersihkanInput($_POST['nama_produk'] ?? '');
    $id_kategori = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : null;
    $sku         = bersihkanInput($_POST['sku'] ?? '');
    $barcode     = bersihkanInput($_POST['barcode'] ?? '');
    $harga_beli  = (float)str_replace(',', '', $_POST['harga_beli'] ?? 0);
    $harga_jual  = (float)str_replace(',', '', $_POST['harga_jual'] ?? 0);
    $deskripsi   = bersihkanInput($_POST['deskripsi'] ?? '');
    if(empty($nama_produk) || empty($id_kategori)) {
        kirimResponseJson(false, "Nama produk dan kategori harus diisi!");
    }
    if(!empty($sku)) {
        $st = $db->prepare("SELECT id_produk FROM produk WHERE sku = ? AND id_produk != ?");
        $st->execute([$sku, $id_produk]);
        if($st->rowCount() > 0) kirimResponseJson(false, "Kode SKU telah terdaftar pada produk lain.");
    }
    if(!empty($barcode)) {
        $st = $db->prepare("SELECT id_produk FROM produk WHERE barcode = ? AND id_produk != ?");
        $st->execute([$barcode, $id_produk]);
        if($st->rowCount() > 0) kirimResponseJson(false, "Barcode Scanner telah diregistrasi pada barang lain.");
    }
    $nama_file_gambar = "";
    $uploadError = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileSize = $_FILES['gambar']['size'];
        if($fileSize > 2097152) {
             kirimResponseJson(false, "Ukuran gambar terlalu jumbo. Maksimum 2MB.");
        }
        $extInfo = pathinfo($fileName, PATHINFO_EXTENSION);
        $ext = strtolower($extInfo);
        $validExt = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $validExt)) {
             kirimResponseJson(false, "Format gambar salah. Mohon hanya upload JPG atau PNG.");
        }
        $namaBaru = uniqid('prod_', true) . '.' . $ext;
        $destinasi = '../uploads/produk/' . $namaBaru;
        if (move_uploaded_file($fileTmp, $destinasi)) {
            $nama_file_gambar = $namaBaru;
        } else {
             kirimResponseJson(false, "Kegagalan saat memindahkan file gambar pada server direktori uploads/produk. Cek permission Write folder.");
        }
    }
    try {
        if ($id_produk > 0) {
            if (!empty($nama_file_gambar)) {
                $sOld = $db->prepare("SELECT gambar FROM produk WHERE id_produk = ?");
                $sOld->execute([$id_produk]);
                $oldImg = $sOld->fetchColumn();
                if(!empty($oldImg) && file_exists('../uploads/produk/' . $oldImg)) {
                    unlink('../uploads/produk/' . $oldImg);
                }
                $sql = "UPDATE produk SET id_kategori=?, sku=?, barcode=?, nama_produk=?, deskripsi=?, harga_beli=?, harga_jual=?, gambar=? WHERE id_produk=?";
                $params = [$id_kategori, $sku, $barcode, $nama_produk, $deskripsi, $harga_beli, $harga_jual, $nama_file_gambar, $id_produk];
            } else {
                $sql = "UPDATE produk SET id_kategori=?, sku=?, barcode=?, nama_produk=?, deskripsi=?, harga_beli=?, harga_jual=? WHERE id_produk=?";
                $params = [$id_kategori, $sku, $barcode, $nama_produk, $deskripsi, $harga_beli, $harga_jual, $id_produk];
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Update produk SKU ($sku)", "produk");
            kirimResponseJson(true, "Data Produk termutahirkan.");
        } else {
            $sql = "INSERT INTO produk (id_kategori, sku, barcode, nama_produk, deskripsi, harga_beli, harga_jual, gambar, stok) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)"; 
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_kategori, $sku, $barcode, $nama_produk, $deskripsi, $harga_beli, $harga_jual, $nama_file_gambar]);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Buat produk baru: ($nama_produk)", "produk");
            kirimResponseJson(true, "Produk berhasil didaftarkan ke peredaran.");
        }
    } catch (PDOException $e) {
         kirimResponseJson(false, "Server Basis Data Error. Permintaan Ditolak.");
    }
}
function hapusProduk($db) {
    $id_produk = isset($_POST['id_produk']) ? (int)$_POST['id_produk'] : 0;
    if($id_produk > 0) {
        try {
            $sOld = $db->prepare("SELECT gambar FROM produk WHERE id_produk = ?");
            $sOld->execute([$id_produk]);
            $oldImg = $sOld->fetchColumn();
            if(!empty($oldImg) && file_exists('../uploads/produk/' . $oldImg)) {
                unlink('../uploads/produk/' . $oldImg);
            }
            $st = $db->prepare("DELETE FROM produk WHERE id_produk = ?");
            $st->execute([$id_produk]);
            require_once '../controllers/AuthController.php';
            catatAuditLog($db, $_SESSION['id_pengguna'], "Erase produk id ($id_produk) terarsip", "produk");
            kirimResponseJson(true, "Katalog produk lenyap seutuhnya.");
        } catch (PDOException $e) {
            kirimResponseJson(false, "Katalog ini tidak dapat ditarik karena terdaftar/terekam pada sistem Transaksi/Faktur sebelumnya (RESTRICT).");
        }
    }
    kirimResponseJson(false, "Identifier tidak valid.");
}
