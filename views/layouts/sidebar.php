<?php
?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><?= htmlspecialchars(getNamaToko()) ?><span class="text-primary">.</span></h3>
            <button class="btn-toggle d-lg-none ms-auto text-dark" id="sidebarCloseBtn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <ul class="list-unstyled components">
            <li class="<?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
                <a href="dashboard.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li class="sidebar-heading mt-3">Master Data</li>
            <li class="<?= ($currentPage === 'kategori') ? 'active' : '' ?>">
                <a href="kategori.php">
                    <i class="bi bi-tags"></i> Kategori
                </a>
            </li>
            <li class="<?= ($currentPage === 'produk') ? 'active' : '' ?>">
                <a href="produk.php">
                    <i class="bi bi-box-seam"></i> Produk
                </a>
            </li>
            <li class="<?= ($currentPage === 'supplier') ? 'active' : '' ?>">
                <a href="supplier.php">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>
            <li class="<?= ($currentPage === 'pelanggan') ? 'active' : '' ?>">
                <a href="pelanggan.php">
                    <i class="bi bi-person-hearts"></i> Pelanggan
                </a>
            </li>
            <li class="sidebar-heading mt-3">Transaksi POS</li>
            <li class="<?= ($currentPage === 'pos') ? 'active' : '' ?>">
                <a href="point_of_sales.php">
                    <i class="bi bi-cart"></i> Kasir (POS)
                </a>
            </li>
            <li class="<?= ($currentPage === 'pembelian') ? 'active' : '' ?>">
                <a href="pembelian.php">
                    <i class="bi bi-cart-plus"></i> Pembelian
                </a>
            </li>
            <li class="sidebar-heading mt-3">Gudang & Stok</li>
            <li class="<?= ($currentPage === 'gudang') ? 'active' : '' ?>">
                <a href="gudang.php">
                    <i class="bi bi-box-seam"></i> Kartu Stok & Mutasi
                </a>
            </li>
            <li class="sidebar-heading mt-3">Laporan</li>
            <li class="<?= ($currentPage === 'laporan') ? 'active' : '' ?>">
                <a href="laporan.php">
                    <i class="bi bi-receipt"></i> Pusat Laporan
                </a>
            </li>
            <li class="sidebar-heading mt-3">Pengaturan</li>
            <li class="<?= ($currentPage === 'pengguna') ? 'active' : '' ?>">
                <a href="pengguna.php">
                    <i class="bi bi-people"></i> User Management
                </a>
            </li>
            <li class="<?= ($currentPage === 'role_akses') ? 'active' : '' ?>">
                <a href="role_akses.php">
                    <i class="bi bi-shield-lock"></i> Hak Akses (Role)
                </a>
            </li>
            <li class="<?= ($currentPage === 'profil') ? 'active' : '' ?>">
                <a href="profil_toko.php">
                    <i class="bi bi-shop"></i> Profil Toko
                </a>
            </li>
            <li class="<?= ($currentPage === 'audit') ? 'active' : '' ?>">
                <a href="audit_log.php">
                    <i class="bi bi-clock-history"></i> Audit Log
                </a>
            </li>
        </ul>
        <div class="px-3 pb-4 pt-3 mt-auto border-top text-center" style="border-color: var(--border-color)!important;">
            <p class="mb-1 fw-bold text-muted small"><?= htmlspecialchars(getNamaToko()) ?> v1.0.0</p>
        </div>
    </nav>
    <div id="content">
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn-toggle" id="sidebarCollapse">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0 ms-3 fw-bold d-none d-md-block" style="color: var(--text-main);"><?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES) ?></h4>
            </div>
            <div class="user-profile dropdown">
                <div class="d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer">
                    <div class="user-info text-end me-3 d-none d-sm-block">
                        <p class="name"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User Web') ?></p>
                        <p class="role"><?= htmlspecialchars($_SESSION['nama_role'] ?? 'Administrator') ?></p>
                    </div>
                    <div class="user-avatar shadow-sm">
                        <?= strtoupper(substr($_SESSION['nama_lengkap'] ?? 'U', 0, 1)) ?>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2" style="border-radius: 12px; min-width: 220px;">
                    <li><h6 class="dropdown-header">Setting Akun</h6></li>
                    <li><a class="dropdown-item py-2" style="border-radius: 8px;" href="profil_saya.php"><i class="bi bi-person me-3 text-muted"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item py-2" style="border-radius: 8px;" href="profil_saya.php"><i class="bi bi-key me-3 text-muted"></i> Ganti Password</a></li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li><a class="dropdown-item py-2 text-danger fw-medium" style="border-radius: 8px;" href="logout.php"><i class="bi bi-box-arrow-right me-3"></i> Keluar</a></li>
                </ul>
            </div>
        </nav>
