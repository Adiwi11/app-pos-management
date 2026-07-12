<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Profil & Otentikasi";
$currentPage = "profil_saya";
$db = Database::dapatkanKoneksi();
$stmt = $db->prepare("SELECT nama_lengkap, email FROM pengguna WHERE id_pengguna = ?");
$stmt->execute([$_SESSION['id_pengguna']]);
$usr = $stmt->fetch(PDO::FETCH_ASSOC);
$csrfToken = buatCsrfToken();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Pengaturan Profil & Keamanan</h3>
            <p class="text-muted m-0">Menyesuaikan nama panggilan sistem dan merotasi sandi privasi Anda.</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:20px;">
                <div class="card-body p-4 p-lg-5">
                    <form id="formProfilSaya">
                        <input type="hidden" name="action" value="update_profil">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-4" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" value="<?= htmlspecialchars($usr['nama_lengkap']) ?>" required>
                            <label for="nama_lengkap">Nama Tampil Penuh</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="email" class="form-control rounded-4 bg-light" id="email" name="email" value="<?= htmlspecialchars($usr['email']) ?>" readonly>
                            <label for="email">ALamat Email Akses (Identitas Mutlak)</label>
                        </div>
                        <h6 class="fw-bold mt-4 mb-3">Ganti Kata Sandi (Kosongkan bila lewat)</h6>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control rounded-4" id="password" name="password" placeholder="Pass Baru">
                            <label for="password">Sandi Pengganti Baru</label>
                        </div>
                        <div class="mt-5 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSimpanProfil">Simpan Perubahan Keamanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
$(document).ready(function() {
    $('#formProfilSaya').on('submit', function(e) {
        e.preventDefault();
        let $btn = $('#btnSimpanProfil');
        let asal = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $.ajax({
            url: 'controllers/PenggunaController.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.sukses) {
                    Swal.fire('Informasi Tersimpan', res.pesan, 'success');
                    $('#password').val('');
                } else {
                    Swal.fire('Oh, Maaf', res.pesan, 'warning');
                }
                $btn.prop('disabled', false).html(asal);
            },
            error: function() {
                Swal.fire('Error HTTP', 'Peladen gagal berkomunikasi.', 'error');
                $btn.prop('disabled', false).html(asal);
            }
        });
    });
});
</script>
