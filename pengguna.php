<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'middleware/rbac_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Manajemen Pengguna";
$currentPage = "pengguna";
$db = Database::dapatkanKoneksi();
$stmtRoles = $db->query("SELECT id_role, nama_role FROM role ORDER BY nama_role ASC");
$listRole = $stmtRoles->fetchAll();
$csrfToken = buatCsrfToken();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="row mb-4">
        <div class="col-12 text-md-start text-center d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h3 class="fw-bold mb-1">Manajemen Pengguna</h3>
                <p class="text-muted m-0">Menambah akun karyawan, kasir, dan penentuan wewenang spesifik.</p>
            </div>
            <button class="btn btn-primary d-inline-flex border-0 align-items-center rounded-pill py-2 px-4 shadow-sm fw-medium" onclick="showModalTambah()" style="background-color: var(--primary);">
                <i class="bi bi-plus-lg me-2"></i> Tambah Pengguna Terdaftar
            </button>
        </div>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelPengguna" class="table table-hover align-middle w-100" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="align-middle">
                            <th class="border-bottom-0 pb-3 text-secondary t-head-modern" style="width:5%">No.</th>
                            <th class="border-bottom-0 pb-3 text-secondary t-head-modern">Nama Pegawai</th>
                            <th class="border-bottom-0 pb-3 text-secondary t-head-modern">SUREL (Email)</th>
                            <th class="border-bottom-0 pb-3 text-secondary t-head-modern">Jabatan / Role</th>
                            <th class="border-bottom-0 pb-3 text-secondary t-head-modern" style="width:12%;">Status</th>
                            <th class="border-bottom-0 pb-3 text-secondary t-head-modern text-center" style="width:12%">Manipulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalPengguna" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom-0 p-4 pb-0">
        <h5 class="modal-title fw-bold" id="modalLabel">Tambah Pengguna Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body p-4 pt-3">
        <form id="formPengguna">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="id_pengguna" id="id_pengguna" value="">
            <div class="form-floating mb-3">
                <input type="text" class="form-control rounded-4" id="nama_lengkap" name="nama_lengkap" placeholder="Nama..." required>
                <label for="nama_lengkap">Nama Lengkap</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control rounded-4" id="email" name="email" placeholder="contoh@mail.com" required>
                <label for="email">Alamat Surel (Email)</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control rounded-4" id="password" name="password" placeholder="Pass">
                <label for="password">Kata Sandi</label>
                <small class="text-muted d-block mt-1 px-1 py-0 float-note-pas">
                    <i class="bi bi-info-circle"></i> Biarkan kosong jika Mode Ubah dan enggan mengganti password
                </small>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <select class="form-select rounded-4" id="id_role" name="id_role" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach($listRole as $role): ?>
                            <option value="<?= $role['id_role'] ?>"><?= htmlspecialchars($role['nama_role']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="id_role">Hak Peran (Role)</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <select class="form-select rounded-4" id="status" name="status" required>
                            <option value="aktif">🟢 Status Aktif</option>
                            <option value="nonaktif">🔴 Dibekukan</option>
                        </select>
                        <label for="status">Kondisi Akun</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-2 border-top text-end">
                <button type="button" class="btn btn-light rounded-pill px-4 me-2 fw-medium" data-bs-dismiss="modal">Batalkan</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-medium" id="btnSimpan">
                    <i class="bi bi-floppy me-1"></i> Simpan File
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let tablePengguna;
let urlList = 'controllers/PenggunaController.php?action=list_data';
$(document).ready(function() {
    // Konfigurasi modern dt
    tablePengguna = $('#tabelPengguna').DataTable({
        ajax: urlList,
        processing: true,
        serverSide: false, // Karena data diturunkan sekaligus dalam contoh ini
        columns: [
            { className: "text-center text-muted" },
            { className: "fw-medium" },
            { className: "text-muted" },
            { },
            { },
            { className: "text-center" }     
        ],
        drawCallback: function(settings) {
            $('.t-head-modern').css({
                'font-weight': '600',
                'font-size' : '0.86rem',
                'text-transform' : 'uppercase'
            });
        }
    });
    // Handle AJAX Form Submit (CUD Data)
    $('#formPengguna').on('submit', function(e) {
        e.preventDefault();
        // Animasi loading tombol
        let $btn = $('#btnSimpan');
        let txtAsli = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
        let fd = new FormData(this);
        $.ajax({
            url: 'controllers/PenggunaController.php?action=simpan',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if(res.sukses) {
                    $('#modalPengguna').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        html: res.pesan, 
                        timer: 1500,
                        showConfirmButton:false
                    });
                    tablePengguna.ajax.reload(null, false);
                } else {
                    Swal.fire({icon: 'error', title: 'Operasi Gagal', text: res.pesan});
                }
                $btn.prop('disabled', false).html(txtAsli);
            },
            error: function(err) {
                 Swal.fire({icon: 'error', title: 'Error Request!', text: 'Gangguan teknis ke server pusat.'});
                 $btn.prop('disabled', false).html(txtAsli);
            }
        });
    });
    // Action Edit Button (delegation)
    $('#tabelPengguna tbody').on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let nama_lengkap = $(this).data('nama');
        let email = $(this).data('email');
        let id_role = $(this).data('role');
        let status = $(this).data('status');
        $('#id_pengguna').val(id);
        $('#nama_lengkap').val(nama_lengkap);
        $('#email').val(email);
        $('#id_role').val(id_role);
        $('#status').val(status);
        $('#password').val('').prop('required', false);
        $('#modalLabel').text('Ubah Data Pengguna');
        $('.float-note-pas').show();
        $('#modalPengguna').modal('show');
    });
    // Action Delete Button
    $('#tabelPengguna tbody').on('click', '.btn-hapus', function() {
        let id = $(this).data('id');
        let tk = $('#csrf_token').val(); // Ambil dari halaman utama form 
        Swal.fire({
            title: 'Hapus Rekam Jejak?',
            text: "Akun akan dihilangkan, Pastikan kembali!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya Binasakan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('controllers/PenggunaController.php?action=hapus', { id_pengguna: id, csrf_token: tk }, function(res) {
                    if(res.sukses) {
                        Swal.fire('Terhapus!', res.pesan, 'success');
                        tablePengguna.ajax.reload(null, false);
                    } else {
                        Swal.fire('Kegagalan', res.pesan, 'error');
                    }
                }, 'json');
            }
        });
    });
});
function showModalTambah() {
    $('#formPengguna')[0].reset();
    $('#id_pengguna').val('');
    $('#password').prop('required', true);
    $('#modalLabel').text('Daftarkan Pengguna Sistem');
    $('.float-note-pas').hide();
    $('#modalPengguna').modal('show');
}
</script>
