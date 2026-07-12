<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Buat Purchase Order";
$currentPage = "pembelian";
$csrfToken = buatCsrfToken();
$db = Database::dapatkanKoneksi();
$supplierStmt = $db->query("SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC");
$listSupplier = $supplierStmt->fetchAll();
$prodStmt = $db->query("SELECT id_produk, barcode, sku, nama_produk, harga_beli FROM produk ORDER BY nama_produk ASC");
$listProduk = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><a href="pembelian.php" class="text-secondary"><i class="bi bi-arrow-left me-2"></i></a> Form Transaksi Pembelian</h3>
            <p class="text-muted m-0 ps-5 ms-1">Mencatat barang restock yang di order ke vendor/supplier.</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary mb-3">Informasi Nota</h6>
                    <form id="formPO">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-4" id="no_nota" name="no_nota" value="PO-<?= date('Ymd-His') ?>" readonly>
                            <label>No. Faktur (Auto)</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control rounded-4" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                            <label>Tanggal Masuk</label>
                        </div>
                        <div class="form-floating mb-4">
                            <select class="form-select rounded-4" id="id_supplier" name="id_supplier" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php foreach($listSupplier as $sup): ?>
                                <option value="<?= $sup['id_supplier'] ?>"><?= htmlspecialchars($sup['nama_supplier']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>Pilih Pemasok (Vendor)</label>
                        </div>
                        <div class="bg-light p-3 rounded-4 border text-center mb-4">
                            <span class="text-muted d-block mb-1 small fw-medium">Total Akhir Tagihan:</span>
                            <h2 class="fw-bold text-primary mb-0" id="labelTotal">Rp0</h2>
                            <input type="hidden" id="total_harga" name="total_harga" value="0">
                        </div>
                        <div class="d-grid mt-2">
                             <button type="button" class="btn btn-primary rounded-pill py-3 fw-bold" id="btnProsesOrder" onclick="simpanTransaksiPembelian()">
                                <i class="bi bi-check-circle me-1"></i> Simpan & Ajukan Konfirmasi Order
                             </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; min-height: 100%;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary mb-3">Item Produk Pembelian</h6>
                    <div class="input-group mb-4 shadow-sm" style="border-radius:12px; overflow:hidden">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <select id="pilihProduk" class="form-control border-start-0 py-3" style="background:#fff">
                            <option value="">Ketik untuk scan / pilih produk...</option>
                            <?php foreach($listProduk as $p): ?>
                                <option value='<?= json_encode($p, JSON_HEX_APOS) ?>'>
                                    [<?= htmlspecialchars($p['sku'] ?? $p['barcode'] ?? '') ?>] - <?= htmlspecialchars($p['nama_produk']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary px-4 fw-medium" type="button" onclick="tambahKeKeranjang()">Tambah (Enter)</button>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle mb-0" id="tabelKeranjang">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-3 text-secondary" style="border-top-left-radius: 12px; font-size:0.85rem">BARANG</th>
                                    <th class="py-3 text-secondary text-end" style="font-size:0.85rem">HARGA MODAL</th>
                                    <th class="py-3 text-secondary text-center" style="font-size:0.85rem; width:15%">QTY</th>
                                    <th class="py-3 text-secondary text-end" style="font-size:0.85rem">SUBTOTAL</th>
                                    <th class="py-3 text-secondary text-center" style="border-top-right-radius: 12px; font-size:0.85rem"><i class="bi bi-gear"></i></th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                <tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-cart-x fs-1 d-block mb-2 text-light"></i>Keranjang masih kosong</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let keranjang = [];
function checkEmpty() {
    if(keranjang.length === 0){ $('#emptyRow').show(); } else { $('#emptyRow').hide(); }
}
function renderCart() {
    $('#cartItems .item-row').remove();
    let totalAll = 0;
    keranjang.forEach((item, index) => {
        let subtotal = item.harga_beli * item.jumlah;
        totalAll += subtotal;
        // Memakai array POST native HTML format name="produk_id[]" sehingga Form Serialize langsung rapi
        let tr = `
        <tr class="item-row">
            <td class="px-3">
                <div class="fw-bold">${item.nama_produk}</div>
                <div class="small text-muted"><i class="bi bi-upc-scan"></i> ${item.barcode || item.sku || '-'}</div>
                <input type="hidden" name="cart_no[]" form="formPO" value="${index}">
                <input type="hidden" name="produk_id[]" form="formPO" value="${item.id_produk}">
            </td>
            <td class="text-end">
                <input type="number" name="harga_beli[]" form="formPO" min="1" class="form-control form-control-sm text-end input-hb fw-medium" value="${item.harga_beli}" data-idx="${index}" style="min-width:100px;">
            </td>
            <td class="text-center">
                <input type="number" name="jumlah[]" form="formPO" min="1" class="form-control form-control-sm text-center fw-bold input-qty mx-auto" value="${item.jumlah}" data-idx="${index}" style="max-width:80px;">
            </td>
            <td class="text-end fw-bold text-success">
                Rp ${(subtotal).toLocaleString('id-ID')}
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light text-danger border" onclick="hapusBaris(${index})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        `;
        $('#cartItems').append(tr);
    });
    checkEmpty();
    $('#labelTotal').text('Rp ' + (totalAll).toLocaleString('id-ID'));
    $('#total_harga').val(totalAll);
}
function tambahKeKeranjang() {
    let raw = $('#pilihProduk').val();
    if(!raw) return Swal.fire('Perhatian','Pilih barang dahulu','warning');
    let obj = JSON.parse(raw);
    // Cek duplikasi didalam js obj keranjang
    let dt = keranjang.find(x => x.id_produk == obj.id_produk);
    if(dt) {
        dt.jumlah += 1;
    } else {
        keranjang.push({
            id_produk: obj.id_produk,
            nama_produk: obj.nama_produk,
            barcode: obj.barcode,
            sku: obj.sku,
            harga_beli: parseFloat(obj.harga_beli) || 0,
            jumlah: 1
        });
    }
    // reset select2/input js dan fokus kembali
    $('#pilihProduk').val('').focus();
    renderCart();
}
function hapusBaris(idx) {
    keranjang.splice(idx, 1);
    renderCart();
}
$(document).ready(function() {
    // Listener dynamic input change (qty dan harga update live)
    $('#cartItems').on('input', '.input-qty, .input-hb', function() {
        let isQty = $(this).hasClass('input-qty');
        let idx = $(this).data('idx');
        let val = parseFloat($(this).val()) || 1;
        if(isQty) { keranjang[idx].jumlah = val; }
        else { keranjang[idx].harga_beli = val; }
        renderCart();
    });
});
function simpanTransaksiPembelian() {
    if(keranjang.length === 0) return Swal.fire('Kosong','Keranjang formulir belum diisi!','warning');
    if(!document.getElementById('formPO').checkValidity()) {
        return document.getElementById('formPO').reportValidity();
    }
    let payload = $('#formPO').serialize();
    let $b = $('#btnProsesOrder'); let asl = $b.html();
    $b.prop('disabled', true).text('Memproses Order...');
    $.post('controllers/PembelianController.php?action=simpan_po', payload, function(res) {
        if(res.sukses) {
            Swal.fire({icon:'success', title:'Transaksi Tersimpan', html:res.pesan, showConfirmButton:false, timer:1500})
            .then(()=>{ window.location.href="pembelian.php"; });
        } else { Swal.fire('Dihentikan!', res.pesan, 'error'); }
        $b.prop('disabled',false).html(asl);
    }, 'json').fail(function() { Swal.fire('Error','Server Time Out','error'); $b.prop('disabled',false).html(asl); });
}
</script>
