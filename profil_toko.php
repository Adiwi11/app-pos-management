<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Profil & Konfigurasi Outlet";
$currentPage = "profil";
$csrfToken = buatCsrfToken();
$db = Database::dapatkanKoneksi();
$konfInfo = $db->query("SELECT * FROM konfigurasi LIMIT 1")->fetch() ?: ['nama_toko'=>'', 'alamat'=>'', 'telepon'=>''];
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-1"><i class="bi bi-shop text-primary me-2"></i> Konfigurasi Cabang / Toko</h3>
            <p class="text-muted mb-4 d-block">Data profil ini akan terukir di kepala setiap Lembar Struk (POS) dan Dokumen Laporan (PDF) yang dicetak mesin.</p>
            <div class="card border-0 shadow-sm" style="border-radius:20px;">
                <div class="card-body p-4 p-md-5">
                    <form id="formProfil">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cursor me-2 text-muted"></i> Informasi Umum</h6>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-4 fw-bold" name="nama_toko" value="<?= htmlspecialchars($konfInfo['nama_toko']) ?>" placeholder="-" required>
                            <label>Nama Merk / Outlet</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control rounded-4 fw-medium text-secondary" name="telepon" value="<?= htmlspecialchars($konfInfo['telepon']) ?>" placeholder="-" required>
                            <label>Telepon Hotline Customer</label>
                        </div>
                        <h6 class="fw-bold text-dark mt-4 mb-3 border-top pt-4"><i class="bi bi-geo-alt me-2 text-muted"></i> Alamat Registrasi (Lokasi)</h6>
                        <div class="form-floating mb-4">
                            <textarea class="form-control rounded-4" name="alamat" style="height: 120px;" placeholder="-" required><?= htmlspecialchars($konfInfo['alamat']) ?></textarea>
                            <label>Tuliskan Jalan, Kelurahan, Kec, Kota beserta Kode Pos Area</label>
                        </div>
                        <div class="d-flex align-items-center bg-light p-3 rounded-4 mt-2 mb-4 text-primary">
                            <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                            <div class="small fw-medium lh-sm">Setiap perubahan profil akan dicatat pada Log Audit Sistem. Pastikan alamat legal Outlet adalah Valid.</div>
                        </div>
                        <div class="d-grid text-end mt-4 pt-2">
                             <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm" id="btnSimpanProfil">
                                <i class="bi bi-floppy-fill me-2"></i> Terapkan Pembaruan
                             </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
$('#formProfil').on('submit', function(e) {
    e.preventDefault();
    let bc = $('#btnSimpanProfil'); let txt = bc.html();
    bc.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
    $.post('controllers/ProfilController.php?action=update', $(this).serialize(), function(r) {
        if(r.sukses) Swal.fire({icon: 'success', title: 'Data Diperbarui!', text: r.pesan, 
            showConfirmButton: false, timer: 1500}).then(()=>{window.location.reload();});
        else Swal.fire('Error', r.pesan, 'error');
        bc.prop('disabled', false).html(txt);
    }, 'json').fail(function(){ 
        Swal.fire('Error Server', 'Fail connection', 'error'); bc.prop('disabled', false).html(txt); 
    });
});
</script>
