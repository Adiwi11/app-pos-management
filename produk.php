<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'middleware/rbac_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Data Produk";
$currentPage = "produk";
$csrfToken = buatCsrfToken();
$db = Database::dapatkanKoneksi();
$stmtKategori = $db->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
$listKategori = $stmtKategori->fetchAll();
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 form-title">Katalog Produk</h3>
            <p class="text-muted m-0">Pengaturan SKU, Barcode, relasi harga, dan manajemen gambar thumbnail.</p>
        </div>
        <button class="btn btn-primary d-inline-flex border-0 align-items-center rounded-pill py-2 px-4 shadow-sm fw-medium" onclick="showModalTambahProduk()" style="background-color: var(--primary);">
            <i class="bi bi-box-seam me-2"></i> Daftarkan Produk
        </button>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabelProduk" class="table table-hover align-middle w-100 mt-2" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="align-middle bg-light">
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern" style="border-top-left-radius: 12px; padding-left:1.5rem">Pic</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Barang & Kategori</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">SKU / Barcode</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Stok Masuk</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern">Harga (Jual/Beli)</th>
                            <th class="border-bottom-0 pb-3 pt-3 text-secondary t-head-modern text-end" style="border-top-right-radius: 12px; padding-right:1.5rem">Opsi Akses</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalLabelProduk" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
      <div class="modal-header border-bottom-0 p-4 pb-0">
        <h5 class="modal-title fw-bold" id="modalLabelProduk">Detail Registrasi Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body p-4 pt-3">
        <form id="formProduk" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <input type="hidden" name="id_produk" id="id_produk" value="">
            <div class="row gx-4">
                <div class="col-lg-4 mb-4 text-center">
                    <div class="d-flex flex-column align-items-center bg-light p-3 rounded-4 border border-dashed h-100 justify-content-center">
                        <img id="gambar_preview" src="assets/placeholder.png" alt="Preview Gambar" class="img-fluid rounded mb-3" style="max-height: 180px; object-fit: cover;">
                        <input type="file" class="form-control form-control-sm rounded mt-auto" id="gambar" name="gambar" accept=".jpg, .jpeg, .png">
                        <small class="text-muted mt-2 d-block w-100" style="font-size:0.75rem;">Maks 2MB (JPG/PNG). Biarkan jika tidak ada perubahan.</small>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-4 fw-medium" id="nama_produk" name="nama_produk" placeholder="Masukkan judul..." required>
                        <label for="nama_produk">Judul / Nama Produk</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select rounded-4 fw-medium" id="id_kategori" name="id_kategori" required>
                            <option value="">-- Pilih Golongan --</option>
                            <?php foreach($listKategori as $kat): ?>
                            <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="id_kategori">Golongan Kategori</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control rounded-4" id="sku" name="sku" placeholder="SKU001">
                                <label for="sku">SKU (ID Internal)</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control rounded-4" id="barcode" name="barcode" placeholder="123456789">
                                <label for="barcode">Barcode Fisik / Scanner</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control rounded-4 text-danger fw-bold" id="harga_beli" name="harga_beli" placeholder="0" min="0" required>
                                <label for="harga_beli">Harga Beli Modal (Rp)</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control rounded-4 text-success fw-bold" id="harga_jual" name="harga_jual" placeholder="0" min="0" required>
                                <label for="harga_jual">Patokan Harga Jual (Rp)</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-1">
                        <textarea class="form-control rounded-4" placeholder="Deskripsi ringkas..." id="deskripsi" name="deskripsi" style="height: 80px"></textarea>
                        <label for="deskripsi">Deksripsi Produk (Opsional)</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top text-end">
                <button type="button" class="btn btn-light rounded-pill px-4 me-2 fw-medium" data-bs-dismiss="modal">Batalkan</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-medium shadow-sm" id="btnSimpanProduk">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Simpan Rekam
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let tableProduk;
let urlListProduk = 'controllers/ProdukController.php?action=list_data';
$(document).ready(function() {
    tableProduk = $('#tabelProduk').DataTable({
        ajax: urlListProduk,
        processing: true,
        columns: [
            { className: "text-center pt-3 pb-3" }, // image
            { className: "fw-medium pt-3" }, // nama
            { className: "text-muted pt-3" }, // sku barcode
            { className: "fw-bold text-dark pt-3" }, // stok
            { className: "pt-3" }, // harga
            { className: "text-end pe-4 pt-3" } // opsi   
        ],
        drawCallback: function(settings) {
            $('.t-head-modern').css({
                'font-weight': '600',
                'font-size' : '0.85rem',
                'text-transform' : 'uppercase'
            });
            $('#tabelProduk tbody td:first-child').css('padding-left', '1.5rem');
        }
    });
    // Handle AJAX Form Submit Multipart Upload
    $('#formProduk').on('submit', function(e) {
        e.preventDefault();
        let $btn = $('#btnSimpanProduk');
        let txtAsli = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah...');
        let fd = new FormData(this);
        $.ajax({
            url: 'controllers/ProdukController.php?action=simpan',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if(res.sukses) {
                    $('#modalProduk').modal('hide');
                    Swal.fire({
                        icon: 'success', title: 'Berhasil', 
                        html: res.pesan, timer: 1500, showConfirmButton:false
                    });
                    tableProduk.ajax.reload(null, false);
                } else {
                    Swal.fire({icon: 'error', title: 'Upload Gagal', text: res.pesan});
                }
                $btn.prop('disabled', false).html(txtAsli);
            },
            error: function(err) {
                 Swal.fire({icon: 'error', title: 'Error Server!', text: 'Gagal merespon peladen HTTP POST Multipart.'});
                 $btn.prop('disabled', false).html(txtAsli);
            }
        });
    });
    // Preview Image Lokal JS
    $("#gambar").change(function() {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                $("#gambar_preview").attr("src", event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    // Handle Edit
    $('#tabelProduk tbody').on('click', '.btn-edit', function() {
        let btn = $(this);
        $('#id_produk').val(btn.data('id'));
        $('#nama_produk').val(btn.data('nama'));
        $('#id_kategori').val(btn.data('kategori'));
        $('#sku').val(btn.data('sku'));
        $('#barcode').val(btn.data('barcode'));
        $('#harga_beli').val(btn.data('hargabeli'));
        $('#harga_jual').val(btn.data('hargajual'));
        $('#deskripsi').val(btn.data('deskripsi'));
        let gmbr = btn.data('gambar');
        if(gmbr) {
            $('#gambar_preview').attr('src', 'uploads/produk/' + gmbr);
        } else {
            $('#gambar_preview').attr('src', 'assets/placeholder.png'); // placeholder jika tidak ada gambar (pastikan file placeholder nnti ada jika perlu)
        }
        $('#modalLabelProduk').text('Edit: ' + btn.data('nama'));
        $('#modalProduk').modal('show');
    });
    // Handle Delete
    $('#tabelProduk tbody').on('click', '.btn-hapus', function() {
        let id = $(this).data('id');
        let tk = $('#csrf_token').val();
        Swal.fire({
            title: 'Tarik Dari Peredaran?',
            text: "Produk akan dihapuskan dari katalog.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya Tarik Keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('controllers/ProdukController.php?action=hapus', { id_produk: id, csrf_token: tk }, function(res) {
                    if(res.sukses) {
                        Swal.fire({icon:'success', title:'Ditarik', text:res.pesan, timer:1500});
                        tableProduk.ajax.reload(null, false);
                    } else {
                        Swal.fire('Kegagalan', res.pesan, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Kesalahan Koneksi', 'Tidak dapat terhubung', 'error');
                });
            }
        });
    });
});
function showModalTambahProduk() {
    $('#formProduk')[0].reset();
    $('#id_produk').val('');
    $('#gambar_preview').attr('src', 'assets/placeholder.png'); // fallback ui
    $('#modalLabelProduk').text('Registrasi Produk Baru');
    $('#modalProduk').modal('show');
}
</script>
