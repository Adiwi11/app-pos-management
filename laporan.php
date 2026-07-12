<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
cekBelumLogin();
$pageTitle = "Laporan Manajemen";
$currentPage = "laporan";
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="mb-4">
        <h3 class="fw-bold form-title text-primary"><i class="bi bi-bar-chart-steps me-2"></i> Pusat Laporan Finansial</h3>
        <p class="text-muted">Cetak rentetan rekapitulasi data POS Penjualan & Restock Pembelian anda dalam kurun waktu tertentu.</p>
    </div>
    <div class="row g-4 pt-2">
        <div class="col-md-6 text-center text-md-start">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5 text-center">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex justify-content-center align-items-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-pie-chart-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Rekap Laporan Penjualan</h4>
                    <p class="text-muted small px-3 mb-4">Omzet / Uang Masuk yang diraup kasir POS dari nota pelanggan beserta rincian pajak (PPN) & diskon total. Dapat difilter by tanggal.</p>
                    <form action="cetak_laporan.php" method="GET" target="_blank" class="p-3 bg-light rounded-4 border">
                        <input type="hidden" name="type" value="penjualan">
                        <div class="row g-2 align-items-center justify-content-center mb-3">
                            <div class="col-sm-5">
                                <label class="small text-muted mb-1 text-start d-block">Dari Tanggal (Mulai)</label>
                                <input type="date" name="mulai" class="form-control rounded-3" value="<?= date('Y-m-01') ?>" required>
                            </div>
                            <div class="col-sm-1 fw-bold text-muted">-</div>
                            <div class="col-sm-5">
                                <label class="small text-muted mb-1 text-start d-block">Sampai (Akhir)</label>
                                <input type="date" name="akhir" class="form-control rounded-3" value="<?= date('Y-m-t') ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm"><i class="bi bi-printer me-1"></i> Terbitkan Laporan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center text-md-start">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5 text-center">
                    <div class="bg-danger bg-opacity-10 text-danger d-inline-flex justify-content-center align-items-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Buku Pengeluaran (Pembelian)</h4>
                    <p class="text-muted small px-3 mb-4">Logistik rekapitulasi penyetoran uang kas ke Supplier untuk menyuplai ulang dan memelihara kesehatan stok.</p>
                    <form action="cetak_laporan.php" method="GET" target="_blank" class="p-3 bg-light rounded-4 border">
                        <input type="hidden" name="type" value="pembelian">
                        <div class="row g-2 align-items-center justify-content-center mb-3">
                            <div class="col-sm-5">
                                <label class="small text-muted mb-1 text-start d-block">Dari Tanggal (Mulai)</label>
                                <input type="date" name="mulai" class="form-control rounded-3" value="<?= date('Y-m-01') ?>" required>
                            </div>
                            <div class="col-sm-1 fw-bold text-muted">-</div>
                            <div class="col-sm-5">
                                <label class="small text-muted mb-1 text-start d-block">Sampai (Akhir)</label>
                                <input type="date" name="akhir" class="form-control rounded-3" value="<?= date('Y-m-t') ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger rounded-pill w-100 fw-bold shadow-sm"><i class="bi bi-printer me-1"></i> Terbitkan Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
