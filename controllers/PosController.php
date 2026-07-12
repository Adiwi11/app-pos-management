<?php
session_start();
require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';
require_once '../helpers/security_helper.php';
require_once '../helpers/response_helper.php';
cekBelumLogin();
$db = Database::dapatkanKoneksi();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (!validasiCsrfToken($_POST['csrf_token'] ?? '')) kirimResponseJson(false, "Token Keamanan Invalid/Kedaluwarsa. Cobalah memuat ulang Halaman Cashier.");
    $id_kasir = (int)$_SESSION['id_pengguna'];
    $id_pelanggan = empty($_POST['id_pelanggan']) ? null : (int)$_POST['id_pelanggan'];
    $cartJson = $_POST['keranjang'] ?? '[]';
    $cart = json_decode($cartJson, true);
    if(empty($cart) || !is_array($cart)) kirimResponseJson(false, "Keranjang Belanjaan Kosong Atribut!");
    $subtotal = (float)$_POST['subtotal'];
    $diskon = (float)$_POST['diskon'];
    $pajakNom = (float)$_POST['pajak_val'];
    $total_bayar = (float)$_POST['total_bayar']; 
    $uang_bayar = (float)$_POST['uang_bayar'];
    $kembalian = (float)$_POST['kembalian'];
    if($uang_bayar < $total_bayar) kirimResponseJson(false, "Uang pembayaran tunai (Cash) tidak mencukupi nominal Tagihan Akhir.");
    $no_invoice = 'INV/' . date('Ymd') . '/' . strtoupper(bin2hex(random_bytes(3)));
    try {
        $db->beginTransaction();
        $stHead = $db->prepare("INSERT INTO penjualan (no_invoice, id_pelanggan, id_pengguna, tanggal, subtotal, diskon, pajak, total_bayar, uang_bayar, kembalian) 
                                VALUES (?,?,?,NOW(),?,?,?,?,?,?)");
        $stHead->execute([$no_invoice, $id_pelanggan, $id_kasir, $subtotal, $diskon, $pajakNom, $total_bayar, $uang_bayar, $kembalian]);
        $id_penjualan = $db->lastInsertId();
        $stDet = $db->prepare("INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah, harga_jual, subtotal) VALUES (?,?,?,?,?)");
        $stKurangiStok = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
        $stLogMutasi = $db->prepare("INSERT INTO riwayat_stok (id_produk, jenis, jumlah, keterangan) VALUES (?, 'keluar', ?, ?)");
        foreach($cart as $item) {
            $subtItem = ((float)$item['harga']) * ((int)$item['qty']);
            $stDet->execute([ $id_penjualan, $item['id_produk'], $item['qty'], $item['harga'], $subtItem ]);
            $stKurangiStok->execute([ $item['qty'], $item['id_produk'] ]);
            $stLogMutasi->execute([ $item['id_produk'], $item['qty'], "Penjualan Eceran/Partai POS No. Nota: $no_invoice" ]);
        }
        $db->commit();
        require_once '../controllers/AuthController.php'; 
        $stLogAud = $db->prepare("INSERT INTO audit_log (id_pengguna, aksi, tabel_terkait, ip_address) VALUES (?,?,?,?)");
        $stLogAud->execute([$id_kasir, "Transaksi Invoice Tercipta ($no_invoice) total Rp.".number_format($total_bayar,0), "penjualan", $_SERVER['REMOTE_ADDR']]);
        echo json_encode(["sukses" => true, "pesan" => "Transaksi Terekam Sistem.", "data_id" => $id_penjualan]);
        exit;
    } catch(Exception $e) {
        $db->rollBack();
        kirimResponseJson(false, "Kegagalan Transaksi Relasional Basis Data PDO: " . $e->getMessage());
    }
} else {
     kirimResponseJson(false, "Methode Request / Endpoint ditolak peladen (Missing Payload).");
}
