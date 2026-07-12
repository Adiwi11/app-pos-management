<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'middleware/rbac_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Data Kategori";
$currentPage = "kategori";
$csrfToken = buatCsrfToken();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="row mb-4 flex-md-row flex-column justify-content-between align-items-md-center">
        <div class="col-auto mb-3 mb-md-0">
            <h3 class="fw-bold mb-1 form-title">Daftar Kategori</h3>
            <p class="text-muted m-0">Susun golongan barang Anda untuk mempermudah inventori stok gudang.</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary d-inline-flex border-0 align-items-center rounded-pill py-2 px-4 shadow-sm fw-medium" onclick="showModalTambahKategori()" style="background-color: var(--primary);">
                <i class="bi bi-tag me-2"></i> Tambah Kategori
            </button>
        </div>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelKategori" class="table table-hover align-middle w-100" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="align-middle bg-light">
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="width:10%; border-top-left-radius: 12px; padding-left:1.5rem">No.</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Nama Kategori Golongan</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end" style="width:20%; border-top-right-radius: 12px; padding-right:1.5rem">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalLabelKategori" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom-0 p-4 pb-0">
        <h5 class="modal-title fw-bold" id="modalLabelKategori">Form Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body p-4 pt-3">
        <form id="formKategori">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="id_kategori" id="id_kategori" value="">
            <div class="form-floating mb-4 mt-2">
                <input type="text" class="form-control rounded-4" id="nama_kategori" name="nama_kategori" placeholder="Nama Kategori" required>
                <label for="nama_kategori">Nama Kategori</label>
            </div>
            <div class="mt-3 pt-2 text-end">
                <button type="button" class="btn btn-light rounded-pill px-4 me-2 fw-medium" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-medium" id="btnSimpanKategori">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Simpan
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let tableKategori;
let urlListKat = 'controllers/KategoriController.php?action=list_data';
$(document).ready(function() {
    tableKategori = $('#tabelKategori').DataTable({
        ajax: urlListKat,
        processing: true,
        columns: [
            { className: "text-muted" },
            { className: "fw-bold form-title" },
            { className: "text-end pe-4" }     
        ],
        drawCallback: function(settings) {
            $('.t-head-modern').css({
                'font-weight': '600',
                'font-size' : '0.85rem',
                'text-transform' : 'uppercase'
            });
            $('#tabelKategori tbody td:first-child').css('padding-left', '1.5rem');
        }
    });
    // Form Submit (Tambh / Ubah)
    $('#formKategori').on('submit', function(e) {
        e.preventDefault();
        let $btn = $('#btnSimpanKategori');
        let txtAsli = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
        $.ajax({
            url: 'controllers/KategoriController.php?action=simpan',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.sukses) {
                    $('#modalKategori').modal('hide');
                    Swal.fire({
                        icon: 'success', title: 'Berhasil', 
                        html: res.pesan, timer: 1500, showConfirmButton:false
                    });
                    tableKategori.ajax.reload(null, false);
                } else {
                    Swal.fire({icon: 'error', title: 'Operasi Gagal', text: res.pesan});
                }
                $btn.prop('disabled', false).html(txtAsli);
            },
            error: function(err) {
                 Swal.fire({icon: 'error', title: 'Error Request!', text: 'Gagal merespon peladen pusat.'});
                 $btn.prop('disabled', false).html(txtAsli);
            }
        });
    });
    // Handle Edit via Delegation
    $('#tabelKategori tbody').on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        $('#id_kategori').val(id);
        $('#nama_kategori').val(nama);
        $('#modalLabelKategori').text('Edit Kategori: ' + nama);
        $('#modalKategori').modal('show');
    });
    // Handle Delete via Delegation
    $('#tabelKategori tbody').on('click', '.btn-hapus', function() {
        let id = $(this).data('id');
        let tk = $('#csrf_token').val();
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Kategori yang dihapus menyebabkan baris produk diset menjadi NULL.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // rose-600
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya Hancurkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('controllers/KategoriController.php?action=hapus', { id_kategori: id, csrf_token: tk }, function(res) {
                    if(res.sukses) {
                        Swal.fire({icon:'success', title:'Dihapus', text:res.pesan, timer:1500});
                        tableKategori.ajax.reload(null, false);
                    } else {
                        Swal.fire('Kegagalan', res.pesan, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Kegagalan', 'Terputus dari database', 'error');
                });
            }
        });
    });
});
function showModalTambahKategori() {
    $('#formKategori')[0].reset();
    $('#id_kategori').val('');
    $('#modalLabelKategori').text('Tambah Kategori Baru');
    $('#modalKategori').modal('show');
}
</script>
