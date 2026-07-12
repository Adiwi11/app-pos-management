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
        $stmt = $db->query("SELECT p.*, s.nama_supplier, u.nama_lengkap as operator 
                            FROM pembelian p 
                            LEFT JOIN supplier s ON p.id_supplier = s.id_supplier
                            LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                            ORDER BY p.tanggal DESC, p.id_pembelian DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = [];
        foreach ($data as $row) {
            $sts = $row['status_approval'];
            if($sts == 'pending') $bS = '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu Bukti</span>';
            elseif($sts == 'approved') $bS = '<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-check-all"></i> Terpasok Gudang</span>';
            else $bS = '<span class="badge bg-danger">Ditolak</span>';
            $btnAksi = '<div class="btn-group">';
            if($sts == 'pending') {
                $btnAksi .= '<button class="btn btn-sm btn-light border text-primary border-end-0 rounded-start btn-approve" data-id="'.$row['id_pembelian'].'" data-nota="'.htmlspecialchars($row['no_nota']).'"><i class="bi bi-shield-check"></i> Accept</button>';
                $btnAksi .= '<button class="btn btn-sm btn-light border text-danger rounded-end btn-hapus" data-id="'.$row['id_pembelian'].'"><i class="bi bi-trash"></i></button>';
            } else {
                $btnAksi .= '<button class="btn btn-sm btn-light border rounded text-secondary" disabled><em>Locked</em></button>';
            }
            $btnAksi .= '</div>';
            $dtFormatted = date('d/m/Y', strtotime($row['tanggal']));
            $response[] = [
                htmlspecialchars($row['no_nota']),
                htmlspecialchars($row['nama_supplier']),
                $dtFormatted,
                htmlspecialchars($row['operator']),
                'Rp ' . number_format($row['total_harga'], 0, ',', '.'),
                $bS,
                $btnAksi
            ];
        }
        echo json_encode(["data" => $response]); exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validasiCsrfToken($_POST['csrf_token'] ?? '')) kirimResponseJson(false, "Sesi Form Invalid.");
    if ($action === 'simpan_po') {
        $no_nota = bersihkanInput($_POST['no_nota']);
        $tanggal = bersihkanInput($_POST['tanggal']);
        $id_sup  = (int)$_POST['id_supplier'];
        $tot_hrg = (float)$_POST['total_harga'];
        $arr_idProd = $_POST['produk_id'] ?? [];
        $arr_qty = $_POST['jumlah'] ?? [];
        $arr_hb = $_POST['harga_beli'] ?? [];
        if(count($arr_idProd) == 0) kirimResponseJson(false, "Tidak ada input keranjang!");
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO pembelian (no_nota, tanggal, id_supplier, id_pengguna, total_harga, status_approval) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$no_nota, $tanggal, $id_sup, $_SESSION['id_pengguna'], $tot_hrg, 'pending']);
            $pembelian_id = $db->lastInsertId();
            $stmtDet = $db->prepare("INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_beli, subtotal) VALUES (?,?,?,?,?)");
            for($i = 0; $i < count($arr_idProd); $i++) {
                $subt = ((int)$arr_qty[$i]) * ((float)$arr_hb[$i]);
                $stmtDet->execute([ $pembelian_id, $arr_idProd[$i], $arr_qty[$i], $arr_hb[$i], $subt ]);
            }
            $db->commit();
            catatAuditLog($db, $_SESSION['id_pengguna'], "Menginput Pembelian PO $no_nota", "pembelian");
            kirimResponseJson(true, "Purchase Order selesai direkam. Berstatus Pending approval masuk.");
        } catch (PDOException $e) {
            $db->rollBack(); kirimResponseJson(false, "Kesalahan server ketika transaksi Database DB: " . $e->getMessage());
        }
    } 
    elseif ($action === 'approve') {
        $id_p = (int)$_POST['id_pembelian'];
        try {
            $db->beginTransaction();
            $sH = $db->prepare("SELECT no_nota, status_approval FROM pembelian WHERE id_pembelian = ? FOR UPDATE");
            $sH->execute([$id_p]);
            $head = $sH->fetch();
            if($head['status_approval'] != 'pending') {
                $db->rollBack(); kirimResponseJson(false, "Nota ini tidak dalam status Valid untuk di-Approve.");
            }
            $nota = $head['no_nota'];
            $db->prepare("UPDATE pembelian SET status_approval = 'approved' WHERE id_pembelian = ?")->execute([$id_p]);
            $detSt = $db->prepare("SELECT id_produk, jumlah FROM detail_pembelian WHERE id_pembelian = ?");
            $detSt->execute([$id_p]);
            $details = $detSt->fetchAll();
            $updStok = $db->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");
            $insLog  = $db->prepare("INSERT INTO riwayat_stok (id_produk, jenis, jumlah, keterangan) VALUES (?, 'masuk', ?, ?)");
            foreach($details as $d) {
                $updStok->execute([ $d['jumlah'], $d['id_produk'] ]);
                $insLog->execute([ $d['id_produk'], $d['jumlah'], "Penerimaan PO Inbound Rekod Nota : $nota" ]);
            }
            $db->commit();
            catatAuditLog($db, $_SESSION['id_pengguna'], "Memverifikasi Stok Masuk dari Nota PO $nota", "riwayat_stok");
            kirimResponseJson(true, "Pengesahan berhasil. Rekod stok telah disinkronisasikan secara otomatis.");
        } catch (PDOException $ex) {
            $db->rollBack(); kirimResponseJson(false, "Fail PDO Error: " . $ex->getMessage());
        }
    } 
    elseif ($action === 'hapus') {
        $id_p = (int)$_POST['id_pembelian'];
        try {
            $db->beginTransaction();
            $sH = $db->prepare("SELECT no_nota, status_approval FROM pembelian WHERE id_pembelian = ? FOR UPDATE");
            $sH->execute([$id_p]);
            $head = $sH->fetch();
            if($head['status_approval'] != 'pending') {
                $db->rollBack(); kirimResponseJson(false, "Hanya Draft PO (Pending status) yang legal dihancurkan lewat Antarmuka user!");
            }
            $db->prepare("DELETE FROM pembelian WHERE id_pembelian=?")->execute([$id_p]);
            $db->commit();
            catatAuditLog($db, $_SESSION['id_pengguna'], "Menghapus PO Batal $head[no_nota]", "pembelian");
            kirimResponseJson(true, "Pesanan Pembelian Dibatalkan/Dihapus.");
        }catch(Exception $ex){
             $db->rollBack(); kirimResponseJson(false, "Fail System: ".$ex->getMessage());
        }
    }
}
function catatAuditLog($db, $idPengguna, $aksi, $tabelTerkait) {
    if(empty($idPengguna)) return; 
    $sql = "INSERT INTO audit_log (id_pengguna, aksi, tabel_terkait, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$idPengguna, $aksi, $tabelTerkait, $_SERVER['REMOTE_ADDR']??'UNKNOWN']);
}
