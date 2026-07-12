<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'middleware/rbac_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Role & Hak Akses";
$currentPage = "role_akses";
$db = Database::dapatkanKoneksi();
$stmtRoles = $db->query("SELECT * FROM role ORDER BY nama_role ASC");
$roles = $stmtRoles->fetchAll();
$csrfToken = buatCsrfToken();
$daftarModul = [
    'dashboard' => 'Ringkasan Dashboard',
    'pengguna'  => 'Manajemen Pengguna',
    'hak_akses' => 'Hak Akses & Role',
    'kategori'  => 'Master Kategori',
    'produk'    => 'Master Produk',
    'supplier'  => 'Data Supplier',
    'pelanggan' => 'Data Pelanggan',
    'pembelian' => 'Transaksi Pembelian',
    'penjualan' => 'Kasir / POS',
    'gudang'    => 'Manajemen Gudang',
    'laporan'   => 'Laporan & Finansial',
    'setting'   => 'Pengaturan Sistem'
];
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Konfigurasi Hak Akses</h3>
        <p class="text-muted m-0">Menentukan level wewenang akses masing-masing peran (role) pada modul.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold m-0 text-primary">Daftar Peran Pengguna</h6>
                        <button class="btn btn-sm btn-light text-primary fw-medium rounded-pill" onclick="tambahRole()"><i class="bi bi-plus"></i> Buat Baru</button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush mt-2" style="border-radius:10px;">
                        <?php foreach($roles as $role): ?>
                            <button type="button" class="list-group-item list-group-item-action role-item border-0 border-bottom py-3 d-flex justify-content-between align-items-center" onclick="pilihRole(<?= $role['id_role'] ?>, '<?= htmlspecialchars($role['nama_role'], ENT_QUOTES) ?>')">
                                <span class="fw-medium text-secondary"><i class="bi bi-person-badge text-primary me-2"></i> <?= htmlspecialchars($role['nama_role']) ?></span>
                                <i class="bi bi-chevron-right text-muted" style="font-size:0.8rem"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 text-center text-muted d-flex flex-column justify-content-center align-items-center" id="emptyStateAkses">
                    <i class="bi bi-shield-lock" style="font-size:4rem; color: #e2e8f0;"></i>
                    <h5 class="mt-3 fw-bold form-title">Pilih Role Pengguna</h5>
                    <p class="small">Silakan pilih peran pada panel di sebelah kiri untuk melihat dan mengganti matriks wewenang akses spesifik terhadap modul-modul <?= htmlspecialchars(getNamaToko()) ?>.</p>
                </div>
                <div class="card-body p-4 d-none" id="formAksesContainer">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">
                        Otorisasi: <span id="namaRoleLabel" class="text-primary">Admin</span>
                    </h5>
                    <form id="formMatriksAkses">
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                        <input type="hidden" name="id_role_target" id="id_role_target" value="">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="ps-3 border-0 rounded-start text-secondary fw-semibold">Modul Sytem</th>
                                        <th class="text-center border-0 text-secondary fw-semibold">L (Lihat)</th>
                                        <th class="text-center border-0 text-secondary fw-semibold">T (Tambah)</th>
                                        <th class="text-center border-0 text-secondary fw-semibold">U (Ubah)</th>
                                        <th class="text-center border-0 rounded-end text-secondary fw-semibold">H (Hapus)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($daftarModul as $key => $title): ?>
                                    <tr>
                                        <td class="ps-3 fw-medium text-dark">
                                            <?= $title ?>
                                            <input type="hidden" name="modul[<?= $key ?>][nama_modul]" value="<?= $key ?>">
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input chk-akses" type="checkbox" name="modul[<?= $key ?>][lihat]" value="1" id="cb_<?= $key ?>_lihat" style="transform: scale(1.3);">
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input chk-akses" type="checkbox" name="modul[<?= $key ?>][tambah]" value="1" id="cb_<?= $key ?>_tambah" style="transform: scale(1.3);">
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input chk-akses" type="checkbox" name="modul[<?= $key ?>][ubah]" value="1" id="cb_<?= $key ?>_ubah" style="transform: scale(1.3);">
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input chk-akses disabled-if-no-lihat" type="checkbox" name="modul[<?= $key ?>][hapus]" value="1" id="cb_<?= $key ?>_hapus" style="transform: scale(1.3);">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 pt-3 text-end border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-medium shadow-sm" id="btnSimpanAkses">
                                <i class="bi bi-floppy me-2"></i> Terapkan Hak Akses
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
function tambahRole() {
    Swal.fire({
        title: 'Buat Peran (Role) Baru',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
            placeholder: 'Contoh: SPG Area, Manager Stok'
        },
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        confirmButtonColor: '#4f46e5',
        showLoaderOnConfirm: true,
        preConfirm: (nama) => {
            if(!nama) {
                Swal.showValidationMessage('Nama role tidak boleh ditiadakan');
            } else {
                return $.post('controllers/RoleController.php?action=tambah_role', { 
                    nama_role: nama, 
                    csrf_token: $('#csrf_token').val() 
                }).then(res => res).catch(err => {
                    if (err.responseJSON) { Swal.showValidationMessage(err.responseJSON.pesan); }
                    else { Swal.showValidationMessage('Gagal menghubungi peladen'); }
                });
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value && result.value.sukses) {
                Swal.fire('Terbuat!', result.value.pesan, 'success').then(()=>{
                    location.reload();
                });
            }
        }
    });
}
function pilihRole(idStr, namaRole) {
    // UI selection class toggle
    $('.role-item').removeClass('bg-primary bg-opacity-10 text-primary border-primary').addClass('text-secondary border-bottom');
    let $targetBtn = $(event.currentTarget);
    $targetBtn.removeClass('text-secondary').addClass('bg-primary bg-opacity-10 text-primary border-primary');
    $targetBtn.find('span').removeClass('text-secondary').addClass('text-primary');
    $('#emptyStateAkses').addClass('d-none').removeClass('d-flex');
    $('#formAksesContainer').removeClass('d-none');
    $('#namaRoleLabel').text(namaRole);
    $('#id_role_target').val(idStr);
    // Animasi Loading Checkbox
    $('.chk-akses').prop('checked', false);
    // Fetch matriks role dari server
    $.getJSON('controllers/RoleController.php?action=get_matriks', { id_role: idStr }, function(res) {
        if(res.sukses && res.data) {
            // Apply cek otomatis
            let arr = res.data;
            arr.forEach((m) => {
                if(m.akses_lihat == 1) $('#cb_' + m.nama_modul + '_lihat').prop('checked', true);
                if(m.akses_tambah == 1) $('#cb_' + m.nama_modul + '_tambah').prop('checked', true);
                if(m.akses_ubah == 1) $('#cb_' + m.nama_modul + '_ubah').prop('checked', true);
                if(m.akses_hapus == 1) $('#cb_' + m.nama_modul + '_hapus').prop('checked', true);
            });
        }
    });
}
$(document).ready(function() {
    $('#formMatriksAkses').on('submit', function(e) {
        e.preventDefault();
        // Simpan state
        let btn = $('#btnSimpanAkses');
        let asli = btn.html();
        btn.prop('disabled', true).text('Menyimpan...');
        let payload = $(this).serialize();
        $.post('controllers/RoleController.php?action=simpan_matriks', payload, function(res) {
            if (res.sukses) {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Disimpan', 
                    text: res.pesan, 
                    timer:1500, 
                    showConfirmButton:false 
                });
            } else {
                 Swal.fire('Gagal', res.pesan, 'error');
            }
            btn.prop('disabled', false).html(asli);
        }, 'json').fail(function(xhr) {
             let ms = xhr.responseJSON ? xhr.responseJSON.pesan : 'Gangguan server.';
             Swal.fire('Error', ms, 'error');
             btn.prop('disabled', false).html(asli);
        });
    });
});
</script>
