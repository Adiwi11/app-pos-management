<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Riwayat Pembelian";
$currentPage = "pembelian";
$csrfToken = buatCsrfToken();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 form-title">Daftar Transaksi Pembelian</h3>
            <p class="text-muted m-0">Catatan faktur belanja dari Supplier, kontrol Restock dan Approval status.</p>
        </div>
        <a href="pembelian_form.php" class="btn btn-primary rounded-pill py-2 px-4 shadow-sm fw-medium d-flex align-items-center">
            <i class="bi bi-cart-plus me-2"></i> Buat Order Baru (PO)
        </a>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelPembelian" class="table table-hover align-middle w-100 mt-2">
                    <thead>
                        <tr class="align-middle bg-light">
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="border-top-left-radius: 12px; padding-left:1.5rem">No. Nota</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Supplier</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Tanggal</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Operator</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Total Rp</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-center">Status</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end" style="border-top-right-radius: 12px; padding-right:1.5rem">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf_val" value="<?= htmlspecialchars($csrfToken) ?>">
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let tablePembelian;
$(document).ready(function() {
    tablePembelian = $('#tabelPembelian').DataTable({
        ajax: 'controllers/PembelianController.php?action=list_data',
        order: [[2, 'desc']], // urut tanggal descending
        columns: [
            { className: "fw-bold form-title ps-4" },
            { className: "fw-medium" },
            { className: "text-muted" },
            { className: "text-muted small" },
            { className: "fw-bold text-success" },
            { className: "text-center" },
            { className: "text-end pe-4" }
        ],
        drawCallback: function() {
            $('.t-head-modern').css({ 'font-weight':'600', 'font-size':'0.85rem', 'text-transform':'uppercase'});
            $('#tabelPembelian tbody td:first-child').css('padding-left', '1.5rem');
        }
    });
    // Approval (Misal: User check fisik -> Accept masuk gudang -> merubah riwayat stok & produk stok)
    $('#tabelPembelian tbody').on('click', '.btn-approve', function() {
        let id_pemb = $(this).data('id');
        let nota = $(this).data('nota');
        Swal.fire({
            title: 'Verifikasi Penerimaan',
            html: "Apakah stok dari Nomor Nota <b>" + nota + "</b> telah mendarat sesuai di gudang? Stok produk akan ditambahkan secara permanen ke inventori.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya, Setujui & Tambah Stok',
            cancelButtonText: 'Batal / Tunda'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('controllers/PembelianController.php?action=approve', { 
                    id_pembelian: id_pemb, 
                    csrf_token: $('#csrf_val').val() 
                }, function(res) {
                    if(res.sukses) {
                        Swal.fire({icon: 'success', title: 'Terverifikasi!', text: res.pesan, timer: 1500});
                        tablePembelian.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.pesan, 'error');
                    }
                }, 'json').fail(function() { Swal.fire('Error', 'Kesalahan Peladen', 'error'); });
            }
        });
    });
    // Delete Pembelian jika pending / kesalahan input
    $('#tabelPembelian tbody').on('click', '.btn-hapus', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Nota?',
            text: "Dapat dihapus apabila status masih Pending.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus'
        }).then((res) => {
            if(res.isConfirmed) {
                $.post('controllers/PembelianController.php?action=hapus', { 
                    id_pembelian: id, 
                    csrf_token: $('#csrf_val').val() 
                }, function(r) {
                    if(r.sukses) { 
                        tablePembelian.ajax.reload(null, false); 
                        Swal.fire('Sukses', r.pesan, 'success'); 
                    } else { Swal.fire('Gagal', r.pesan, 'error'); }
                }, 'json');
            }
        });
    });
});
</script>
