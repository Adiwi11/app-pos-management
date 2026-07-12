<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
cekBelumLogin();
$pageTitle = "Log Kejadian Sistem";
$currentPage = "audit";
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold form-title"><i class="bi bi-shield-check text-success me-2"></i> Audit Log System</h3>
            <p class="text-muted m-0">Menelusuri dan mengamankan tindakan krusial pengguna pada sistem aplikasi <?= htmlspecialchars(getNamaToko()) ?> (Non-destructive Read Only Mode).</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelAudit" class="table table-hover align-middle w-100 mt-2">
                    <thead>
                        <tr class="align-middle bg-light">
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="border-top-left-radius: 12px; padding-left:1.5rem">Timestamp</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">User (Email-Identitas)</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Aksi Jurnal Histori</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end">Tujuan (Tabel)</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end" style="border-top-right-radius: 12px; padding-right:1.5rem">IP Pelaku</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="alert bg-primary bg-opacity-10 text-primary mt-4 border-0 d-flex align-items-center" role="alert" style="border-radius:15px;">
              <i class="bi bi-info-circle-fill fs-5 me-3"></i>
              <div>Data audit hanya bisa ditambahkan otomatis oleh sistem API Triggers & dilarang keras dihapus melalui UI guna memelihara integritas keamanan Database ERP!</div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
$(document).ready(function() {
    $('#tabelAudit').DataTable({
        ajax: 'controllers/SystemController.php?action=list_audit',
        order: [[0, 'desc']], // Paling Mutahir
        columns: [
            { className: "fw-medium ps-4 text-muted border-end border-light w-25" },
            { className: "fw-bold form-title w-25" },
            { className: "w-50 text-wrap lh-sm" },
            { className: "text-end text-muted small fw-bold" }, 
            { className: "text-end pe-4 small text-secondary" }
        ],
        drawCallback: function() {
            $('.t-head-modern').css({ 'font-weight':'600', 'font-size':'0.85rem', 'text-transform':'uppercase'});
            $('#tabelAudit tbody td:first-child').css('padding-left', '1.5rem');
        }
    });
});
</script>
