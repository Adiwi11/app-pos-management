<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/database.php';
if(!isset($_SESSION['id_pengguna'])) {
    die("Bukan Kasir Sah.");
}
$id_penjualan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id_penjualan <= 0) die("Invoice Tidak Ditemukan.");
$db = Database::dapatkanKoneksi();
$sHead = $db->prepare("
    SELECT p.*, pel.nama_pelanggan, kas.nama_lengkap as kasir 
    FROM penjualan p
    LEFT JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    LEFT JOIN pengguna kas ON p.id_pengguna = kas.id_pengguna
    WHERE p.id_penjualan = ?
");
$sHead->execute([$id_penjualan]);
$trans = $sHead->fetch(PDO::FETCH_ASSOC);
if(!$trans) die("Data Transaksi Tidak Tersedia / Sudah dihapus.");
$sDet = $db->prepare("
    SELECT d.jumlah, d.harga_jual, d.subtotal, pr.nama_produk 
    FROM detail_penjualan d
    JOIN produk pr ON d.id_produk = pr.id_produk
    WHERE d.id_penjualan = ?
");
$sDet->execute([$id_penjualan]);
$items = $sDet->fetchAll(PDO::FETCH_ASSOC);
function rp($val) {
    return number_format($val, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #<?= htmlspecialchars($trans['no_invoice']) ?></title>
    <style>
        /* Desain Untuk Cetak Mini Printer Thermal 58mm */
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px; /* Ukuran standard baca struk */
            color: #000;
            width: 58mm; 
            max-width: 58mm; /* Menjaga batas thermal limit */
            background-color: #fff;
        }
        @media print {
            body { width: 100%; max-width: 100%; margin: 0; padding: 0; padding-top: 5mm;}
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .margin-top { margin-top: 10px; }
        .margin-bottom { margin-bottom: 10px; }
        .garis {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0;}
        .toko-title {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 2px;
        }
    </style>
</head>
<body onload="window.print()"> 
    <div class="text-center margin-bottom">
        <div class="toko-title"><?= htmlspecialchars($toko['nama_toko']) ?></div>
        <div><?= htmlspecialchars($toko['alamat'] ?? 'Alamat Kosong') ?></div>
        <div>Telp: <?= htmlspecialchars($toko['telepon'] ?? '-') ?></div>
    </div>
    <div class="garis"></div>
    <table>
        <tr>
            <td>No</td>
            <td>: <?= htmlspecialchars($trans['no_invoice']) ?></td>
        </tr>
        <tr>
            <td>Tgl</td>
            <td>: <?= date('d-m-Y H:i', strtotime($trans['tanggal'])) ?></td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>: <?= htmlspecialchars(substr($trans['kasir'],0,15)) ?></td>
        </tr>
        <?php if(!empty($trans['nama_pelanggan'])): ?>
        <tr>
            <td>Plg</td>
            <td>: <?= htmlspecialchars(substr($trans['nama_pelanggan'],0,15)) ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <div class="garis"></div>
    <table style="margin-bottom:5px;">
        <?php foreach($items as $i): ?>
        <tr>
            <td colspan="3" class="bold" style="padding-bottom:1px;"><?= htmlspecialchars($i['nama_produk']) ?></td>
        </tr>
        <tr>
            <td style="width:15%"><?= $i['jumlah'] ?>x</td>
            <td style="width:35%"><?= rp($i['harga_jual']) ?></td>
            <td class="text-right bold"><?= rp($i['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="garis"></div>
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right"><?= rp($trans['subtotal']) ?></td>
        </tr>
        <?php if($trans['diskon'] > 0): ?>
        <tr>
            <td>Diskon</td>
            <td class="text-right">- <?= rp($trans['diskon']) ?></td>
        </tr>
        <?php endif; ?>
        <?php if($trans['pajak'] > 0): ?>
        <tr>
            <td>PPN</td>
            <td class="text-right">+ <?= rp($trans['pajak']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="bold margin-top" style="font-size: 15px;">TOTAL</td>
            <td class="text-right bold margin-top" style="font-size: 15px;">Rp <?= rp($trans['total_bayar']) ?></td>
        </tr>
    </table>
    <div class="garis"></div>
    <table>
        <tr>
            <td>Tunai</td>
            <td class="text-right"><?= rp($trans['uang_bayar']) ?></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right"><?= rp($trans['kembalian']) ?></td>
        </tr>
    </table>
    <div class="text-center margin-top margin-bottom" style="margin-top: 15px;">
        <div>Terima Kasih Telah Berbelanja!</div>
        <div style="font-size: 10px; margin-top:5px; color:#555;">Sistem POS Ditenagai oleh <?= htmlspecialchars($toko['nama_toko'] ?? 'Sistem') ?></div>
    </div>
</body>
</html>
