<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Data Pelanggan";
$currentPage = "pelanggan";
$csrfToken = buatCsrfToken();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 form-title">Data Pelanggan</h3>
            <p class="text-muted m-0">Susun Member/Pelanggan setia toko Anda.</p>
        </div>
        <button class="btn btn-primary rounded-pill py-2 px-4 shadow-sm fw-medium d-flex align-items-center" onclick="showModalPelanggan()">
            <i class="bi bi-person-hearts me-2"></i> Tambah Pelanggan Member
        </button>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius:20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelPelanggan" class="table table-hover align-middle w-100 mt-2">
                    <thead>
                        <tr class="align-middle bg-light">
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="border-top-left-radius: 12px; padding-left:1.5rem">Nama Lengkap Pelanggan</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">No HP Aktif</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Alamat Pengiriman</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end" style="border-top-right-radius: 12px; padding-right:1.5rem">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalPelanggan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow border-0" style="border-radius:20px;">
      <div class="modal-header border-bottom-0 p-4 pb-0">
        <h5 class="modal-title fw-bold" id="modalLabelPelanggan">Form Pelanggan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 pt-3">
        <form id="formPelanggan">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <input type="hidden" name="id_pelanggan" id="id_pelanggan" value="">
            <div class="form-floating mb-3 mt-2">
                <input type="text" class="form-control rounded-4" id="nama_pelanggan" name="nama_pelanggan" placeholder="..." required>
                <label for="nama_pelanggan">Nama Pelanggan (Individu/PT)</label>
            </div>
            <div class="form-floating mb-3 mt-2">
                <input type="text" class="form-control rounded-4" id="no_telp" name="no_telp" placeholder="...">
                <label for="no_telp">No. Ponsel (WA)</label>
            </div>
            <div class="form-floating mb-3 mt-2">
                <textarea class="form-control rounded-4" id="alamat" name="alamat" placeholder="..." style="height:100px"></textarea>
                <label for="alamat">Alamat Tinggal / Kantor</label>
            </div>
            <div class="mt-4 pt-2 text-end">
                <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm" id="btnSimpanPelanggan">Terapkan</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let tablePelanggan;
$(document).ready(function() {
    tablePelanggan = $('#tabelPelanggan').DataTable({
        ajax: 'controllers/PelangganController.php?action=list_data',
        columns: [
            { className: "fw-bold form-title" },
            { className: "text-muted" },
            { className: "text-muted small" },
            { className: "text-end pe-4" }     
        ],
        drawCallback: function() {
            $('.t-head-modern').css({ 'font-weight':'600', 'font-size':'0.85rem', 'text-transform':'uppercase'});
            $('#tabelPelanggan tbody td:first-child').css('padding-left', '1.5rem');
        }
    });
    $('#formPelanggan').on('submit', function(e) {
        e.preventDefault();
        let $btn = $('#btnSimpanPelanggan'); let asli = $btn.html();
        $btn.prop('disabled', true).text('Menyimpan...');
        $.post('controllers/PelangganController.php?action=simpan', $(this).serialize(), function(res) {
            if(res.sukses) {
                $('#modalPelanggan').modal('hide');
                Swal.fire({icon:'success', title:'Success', html:res.pesan, timer:1500, showConfirmButton:false});
                tablePelanggan.ajax.reload(null, false);
            } else { Swal.fire('Error', res.pesan, 'error'); }
            $btn.prop('disabled', false).html(asli);
        }, 'json').fail(function() { Swal.fire('Error','Server fail','error'); $btn.prop('disabled',false).html(asli); });
    });
    $('#tabelPelanggan tbody').on('click', '.btn-edit', function() {
        $('#id_pelanggan').val($(this).data('id'));
        $('#nama_pelanggan').val($(this).data('nama'));
        $('#no_telp').val($(this).data('telp'));
        $('#alamat').val($(this).data('alamat'));
        $('#modalLabelPelanggan').text('Edit: ' + $(this).data('nama'));
        $('#modalPelanggan').modal('show');
    });
    $('#tabelPelanggan tbody').on('click', '.btn-hapus', function() {
        let id = $(this).data('id'); let tk = $('#csrf_token').val();
        Swal.fire({title:'Hapus Hubungan Pelanggan?', icon:'warning', showCancelButton:true, confirmButtonColor:'#e11d48', confirmButtonText:'Ya Hapuskan'}).then((res) => {
            if (res.isConfirmed) {
                $.post('controllers/PelangganController.php?action=hapus', { id_pelanggan: id, csrf_token: tk }, function(r) {
                    if(r.sukses) { tablePelanggan.ajax.reload(null,false); Swal.fire('Berhasil Terhapus',r.pesan,'success'); } else { Swal.fire('Gagal',r.pesan,'error'); }
                }, 'json');
            }
        });
    });
});
function showModalPelanggan() { $('#formPelanggan')[0].reset(); $('#id_pelanggan').val(''); $('#modalLabelPelanggan').text('Pelanggan Baru'); $('#modalPelanggan').modal('show'); }
</script>
