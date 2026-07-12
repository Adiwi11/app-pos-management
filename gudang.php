<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
cekBelumLogin();
$pageTitle = "Kartu Stok & Mutasi Gudang";
$currentPage = "gudang";
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 form-title">Kartu Stok (Mutasi)</h3>
            <p class="text-muted m-0">Meninjau pergerakan rekam jejak barang masuk (Inbound) dan keluar (Outbound).</p>
        </div>
        <a href="stock_opname.php" class="btn btn-primary rounded-pill py-2 px-4 shadow-sm fw-medium d-flex align-items-center">
            <i class="bi bi-box-seam me-2"></i> Lakukan Stock Opname
        </a>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelMutasi" class="table table-hover align-middle w-100 mt-2">
                    <thead>
                        <tr class="align-middle bg-light">
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="border-top-left-radius: 12px; padding-left:1.5rem">Tgl Mutasi</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Barang Item</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-center">Tipe</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end">Volume / Qty</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="border-top-right-radius: 12px;">Referensi Keterangan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
$(document).ready(function() {
    $('#tabelMutasi').DataTable({
        ajax: 'controllers/GudangController.php?action=list_mutasi',
        order: [[0, 'desc']], // Urut tanggal
        columns: [
            { className: "fw-medium ps-4 text-muted border-end border-light" },
            { className: "fw-bold form-title" },
            { className: "text-center" }, // badges
            { className: "text-end fw-bold" }, 
            { className: "text-muted small ps-3" }
        ],
        drawCallback: function() {
            $('.t-head-modern').css({ 'font-weight':'600', 'font-size':'0.85rem', 'text-transform':'uppercase'});
            $('#tabelMutasi tbody td:first-child').css('padding-left', '1.5rem');
        }
    });
});
</script>
