<?php
session_start();
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
if (!isset($_SESSION['id_pengguna'])) die("Akses Ditolak.");
$type = $_GET['type'] ?? 'penjualan';
$mulai = $_GET['mulai'] ?? date('Y-m-01');
$akhir = $_GET['akhir'] ?? date('Y-m-t');
$db = Database::dapatkanKoneksi();
$konf = $db->query("SELECT nama_toko, alamat, telepon FROM konfigurasi LIMIT 1")->fetch(PDO::FETCH_ASSOC);
function rp($v) { return number_format((float)$v, 0, ',', '.'); }
$data = []; $totalAmt = 0; $titleType = "";
if($type === 'penjualan') {
    $st = $db->prepare("SELECT p.tanggal, p.no_invoice, pel.nama_pelanggan, kas.nama_lengkap as kasir, p.total_bayar 
                        FROM penjualan p 
                        LEFT JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan 
                        LEFT JOIN pengguna kas ON p.id_pengguna = kas.id_pengguna 
                        WHERE DATE(p.tanggal) >= ? AND DATE(p.tanggal) <= ? 
                        ORDER BY p.tanggal ASC");
    $st->execute([$mulai, $akhir]);
    $data = $st->fetchAll(PDO::FETCH_ASSOC);
    $titleType = "REKAPITULASI PENJUALAN KASIR (POS)";
} else {
    $st = $db->prepare("SELECT p.tanggal, p.no_nota as no_invoice, sup.nama_supplier as nama_pelanggan, kas.nama_lengkap as kasir, p.total_harga as total_bayar 
                        FROM pembelian p 
                        LEFT JOIN supplier sup ON p.id_supplier = sup.id_supplier 
                        LEFT JOIN pengguna kas ON p.id_pengguna = kas.id_pengguna 
                        WHERE DATE(p.tanggal) >= ? AND DATE(p.tanggal) <= ? AND p.status_approval = 'approved' 
                        ORDER BY p.tanggal ASC");
    $st->execute([$mulai, $akhir]);
    $data = $st->fetchAll(PDO::FETCH_ASSOC);
    $titleType = "REKAPITULASI PEMBELIAN MASUK (BARANG)";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan - <?= htmlspecialchars($konf['nama_toko'] ?? 'Toko POS') ?></title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, Helvetica, sans-serif; color: #333; font-size: 13px; margin:0; }
        .header-print { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header-print h1 { margin: 0 0 5px 0; font-size: 24px; }
        .header-print p { margin: 2px 0; color: #555; }
        .info-baca { margin-bottom: 20px; font-weight: bold; }
        table.tbl-laporan { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.tbl-laporan th, table.tbl-laporan td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table.tbl-laporan th { background-color: #f8f9fa; text-align: center!important; font-weight: bold; }
        table.tbl-laporan td.num { text-align: right!important; font-weight: bold; }
        .t-foot th { font-size: 14px; background-color: #eee!important; }
        .ttd-box { float: right; width: 250px; text-align: center; margin-top: 40px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header-print">
        <h1><?= htmlspecialchars($konf['nama_toko'] ?? 'Toko POS') ?></h1>
        <p><?= htmlspecialchars($konf['alamat'] ?? '-') ?></p>
        <p>Telepon: <?= htmlspecialchars($konf['telepon'] ?? '-') ?></p>
        <h3 style="margin-top:20px; margin-bottom:0px; text-decoration: underline;"><?= $titleType ?></h3>
    </div>
    <div class="info-baca">
        Periode Laporan: <?= date('d/m/Y', strtotime($mulai)) ?> s.d <?= date('d/m/Y', strtotime($akhir)) ?>
    </div>
    <table class="tbl-laporan">
        <thead>
            <tr>
                <th style="width:5%">NO</th>
                <th>TANGGAL TRANSAKSI</th>
                <th>NOMOR REFERENSI</th>
                <th>KLIEN / SUPPLIER</th>
                <th>OPERATOR</th>
                <th style="width:20%">TOTAL (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(count($data)===0) { echo "<tr><td colspan='6' style='text-align:center'>Nihil Transaksi.</td></tr>"; }
            $no=1; 
            foreach($data as $d): 
                $totalAmt += (float)$d['total_bayar'];
            ?>
            <tr>
                <td style="text-align:center"><?= $no++ ?></td>
                <td><?= date('d/m/Y H:i', strtotime($d['tanggal'])) ?></td>
                <td style="font-family:monospace;"><?= htmlspecialchars($d['no_invoice']) ?></td>
                <td><?= htmlspecialchars($d['nama_pelanggan'] ? $d['nama_pelanggan'] : 'NON MEMBER') ?></td>
                <td><?= htmlspecialchars($d['kasir']) ?></td>
                <td class="num"><?= rp($d['total_bayar']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="t-foot">
                <th colspan="5" style="text-align:right!important">GRAND TOTAL TERKUMPUL</th>
                <th class="num" style="text-align:right!important">Rp <?= rp($totalAmt) ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="ttd-box">
        <p>Tercetak: <?= date('d M Y') ?></p>
        <br><br><br><br>
        <p style="text-decoration: underline; font-weight: bold; margin-bottom:0;"> ( . . . . . . . . . . . . . . . . . ) </p>
        <span>Manajer / Pimpinan Operasional</span>
    </div>
</body>
</html>
