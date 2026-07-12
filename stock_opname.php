<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Penyesuaian Fisik Gudang";
$currentPage = "gudang";
$csrfToken = buatCsrfToken();
$db = Database::dapatkanKoneksi();
$pSt = $db->query("SELECT id_produk, sku, nama_produk, stok FROM produk ORDER BY nama_produk ASC");
$prod = $pSt->fetchAll();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><a href="gudang.php" class="text-secondary"><i class="bi bi-arrow-left me-2"></i></a> Stock Opname (Penyesuaian Fisik)</h3>
        <p class="text-muted m-0 ms-5 ps-1">Form sinkronisasi manual apabila persediaan sistem tidak selaras dengan fisik.</p>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px; max-width:800px;">
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold text-primary mb-4 border-bottom pb-3"><i class="bi bi-tools me-2"></i> Formulir Adjustment Stok</h5>
            <form id="formOpname">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                <div class="form-floating mb-4">
                    <select class="form-select rounded-4 fw-medium" id="id_produk" name="id_produk" onchange="autoLoadStok()" required>
                        <option value="">-- Pilih Barang Cacat/Hilang/Berlebih --</option>
                        <?php foreach($prod as $p): ?>
                        <option value="<?= $p['id_produk'] ?>" data-s="<?= $p['stok'] ?>">
                           [<?= htmlspecialchars($p['sku']) ?>] - <?= htmlspecialchars($p['nama_produk']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Pilih Barang / Target SKU</label>
                </div>
                <div class="row mb-4 bg-light mx-0 p-3 rounded-4 border justify-content-center text-center">
                    <div class="col-6 border-end">
                        <span class="text-muted d-block small mb-1">Stok Komputer (Sistem)</span>
                        <h2 class="fw-bold m-0" id="txStokSistem">-</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-select rounded-4 fw-medium text-danger" name="jenis" required>
                                <option value="keluar">STOK BERKURANG (Barang Hilang/Rusak/Expired)</option>
                                <option value="masuk">STOK BERTAMBAH (Barang Ditemukan Lebih)</option>
                            </select>
                            <label>Jenis Penyesuaian</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="number" class="form-control rounded-4 text-primary fs-5 fw-bold" name="jumlah_selisih" min="1" placeholder="1" required>
                            <label>Jumlah Selisih (Qty Absolut)</label>
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-4">
                    <textarea class="form-control rounded-4" name="keterangan" placeholder="..." style="height: 100px" required></textarea>
                    <label>Berikan Alasan Audit (Wajib)</label>
                </div>
                <div class="d-grid pt-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold border-0 shadow-sm" id="btnProses">Eksekusi Penyesuaian Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
function autoLoadStok() {
    let s = $('#id_produk option:selected').data('s');
    if(s !== undefined) $('#txStokSistem').text(s); else $('#txStokSistem').text('-');
}
$('#formOpname').on('submit', function(e) {
    e.preventDefault();
    if(!$('#id_produk').val()) return;
    let bc = $('#btnProses'); let ab = bc.text();
    bc.prop('disabled', true).text('Memproses Audit...');
    $.post('controllers/GudangController.php?action=opname', $(this).serialize(), function(r) {
        if(r.sukses) {
            Swal.fire({icon:'success', title:'Opname Dicatat', text:r.pesan, timer:2000, showConfirmButton:false}).then(()=> { window.location.href="gudang.php"; });
        } else Swal.fire('Error', r.pesan, 'error');
        bc.prop('disabled', false).text(ab);
    }, 'json').fail(function(){ 
        Swal.fire('Error Server', 'Fail connection', 'error'); bc.prop('disabled', false).text(ab); 
    });
});
</script>
