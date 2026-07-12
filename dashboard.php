<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
cekBelumLogin();
$pageTitle = "Dashboard";
$currentPage = "dashboard";
$db = Database::dapatkanKoneksi();
$stmtUser = $db->query("SELECT COUNT(id_pengguna) FROM pengguna WHERE status = 'aktif'");
$totalUser = $stmtUser->fetchColumn();
$totalProduk = $db->query("SELECT COUNT(id_produk) FROM produk")->fetchColumn();
$totalSales = $db->query("SELECT COALESCE(SUM(total_bayar), 0) FROM penjualan WHERE DATE(tanggal) = CURDATE()")->fetchColumn();
$sqlLaba = "SELECT COALESCE(SUM((dp.harga_jual - p.harga_beli) * dp.jumlah), 0) 
            FROM detail_penjualan dp 
            JOIN penjualan trx ON dp.id_penjualan = trx.id_penjualan 
            JOIN produk p ON dp.id_produk = p.id_produk 
            WHERE DATE(trx.tanggal) = CURDATE()";
$labaKotor = $db->query($sqlLaba)->fetchColumn();
$chartLabels = [];
$chartData = [];
$hariIndo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
for($i = 6; $i >= 0; $i--) {
    $tglKalkulasi = date('Y-m-d', strtotime("-$i days"));
    $namaHari = date('l', strtotime($tglKalkulasi));
    $stmtChart = $db->prepare("SELECT COALESCE(SUM(total_bayar), 0) FROM penjualan WHERE DATE(tanggal) = ?");
    $stmtChart->execute([$tglKalkulasi]);
    $omzetHarian = $stmtChart->fetchColumn();
    $chartLabels[] = $hariIndo[$namaHari];
    $chartData[] = $omzetHarian;
}
$jsonLabels = json_encode($chartLabels);
$jsonData = json_encode($chartData);
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-2">Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>! 👋</h3>
            <p class="text-muted mb-0">Berikut ini ringkasan analitik dan stok toko grosir Anda secara real-time.</p>
        </div>
        <div>
            <button class="btn btn-light shadow-sm bg-white" style="border-radius:12px; pointer-events:none;">
                <i class="bi bi-calendar3 text-primary me-2"></i> 
                <span class="fw-medium"><?= date('d F Y') ?></span>
            </button>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <p class="stat-title">Omzet Hari Ini</p>
                    <h4 class="stat-value">Rp <?= number_format($totalSales, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <p class="stat-title">Laba Kotor Hari Ini</p>
                    <h4 class="stat-value">Rp <?= number_format($labaKotor, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <p class="stat-title">Produk Terdaftar</p>
                    <h4 class="stat-value"><?= number_format($totalProduk) ?> Barcode</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <p class="stat-title">Pengguna Sistem</p>
                    <h4 class="stat-value"><?= number_format($totalUser) ?> Akun</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 form-title">Trend Penjualan (7 Hari)</h5>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Filter Detail</button>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="salesTrendsChart" width="100%" height="100%"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 form-title">Notifikasi Sistem</h5>
                    <div class="alert alert-light text-center border mt-3" style="border-radius:14px; padding:2rem 1rem;">
                        <i class="bi bi-check-circle-fill text-success d-block mb-3" style="font-size:2.5rem;"></i>
                        <h6 class="fw-bold">Stok Barang Aman!</h6>
                        <p class="text-muted small mb-0 mt-2">Tidak ditemukan produk yang jumlah batas minimumnya hampir habis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('salesTrendsChart');
    if(ctx){
        const kontek = ctx.getContext('2d');
        const gradient = kontek.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)'); // Indigo transparent
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
        new Chart(kontek, {
            type: 'line',
            data: {
                labels: <?= $jsonLabels ?>,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: <?= $jsonData ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4, // smooth curve
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { family: 'Outfit', size: 14 },
                        bodyFont: { family: 'Outfit', size: 13 },
                        displayColors: false,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#e2e8f0',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'Outfit', size: 12 },
                            callback: function(value) {
                                return value / 1000000 + 'Jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'Outfit', size: 12 }
                        }
                    }
                }
            }
        });
    }
});
</script>
