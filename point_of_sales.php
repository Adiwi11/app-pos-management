<?php
require_once 'config/database.php';
require_once 'middleware/auth_middleware.php';
require_once 'helpers/security_helper.php';
cekBelumLogin();
$pageTitle = "Point of Sales";
$currentPage = "pos";
$csrfToken = buatCsrfToken();
$db = Database::dapatkanKoneksi();
$stmtP = $db->query("SELECT id_pelanggan, nama_pelanggan FROM pelanggan ORDER BY nama_pelanggan ASC");
$pelanggan = $stmtP->fetchAll(PDO::FETCH_ASSOC);
$stmtPr = $db->query("SELECT id_produk, barcode, sku, nama_produk, harga_jual, stok, gambar FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
$produk = $stmtPr->fetchAll(PDO::FETCH_ASSOC);
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar.php';
?>
<style>
    .pos-product-card:hover { transform: translateY(-4px); transition: all 0.2s; cursor: pointer; border-color: var(--primary) !important; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
    .pos-product-card { transition: all 0.2s; border: 2px solid transparent; }
    .pos-sidebar-right { height: calc(100vh - 120px); top: 100px; padding-bottom: 2rem; display: flex; flex-direction: column; }
    .cart-wrapper { flex-grow: 1; overflow-y: auto; overflow-x: hidden; }
    .pos-summary { border-top: 2px dashed #dee2e6; margin-top: auto; padding-top: 1rem; }
</style>
<div class="main-content">
    <div class="row g-4 h-100 pb-4">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-3 d-flex align-items-center form-title text-primary"><i class="bi bi-display me-2 fs-2"></i> Kasir Terminal</h3>
            <div class="input-group mb-4 shadow-sm" style="border-radius:16px; overflow:hidden">
                <span class="input-group-text bg-white border-0 py-3"><i class="bi bi-upc-scan fs-5 text-muted"></i></span>
                <input type="text" id="inputPencarian" class="form-control border-0 py-3 fs-5" placeholder="Scan Barcode / Ketik Produk (F2 untuk fokus)" autofocus autocomplete="off">
            </div>
            <div class="row g-3" id="katalogProduk">
                <?php foreach($produk as $p): 
                    $gb = empty($p['gambar']) ? 'assets/placeholder.png' : 'uploads/produk/'.$p['gambar']; 
                    $jsonStr = htmlspecialchars(json_encode([
                        'id_produk'  => $p['id_produk'],
                        'nama_produk'=> $p['nama_produk'],
                        'harga_jual' => $p['harga_jual'],
                        'stok'       => $p['stok']
                    ]), ENT_QUOTES);
                ?>
                <div class="col-md-4 col-sm-6 p-item" data-barcode="<?= strtolower($p['barcode']??'') ?>" data-nama="<?= strtolower($p['nama_produk']) ?>" data-sku="<?= strtolower($p['sku']??'') ?>">
                    <div class="card h-100 shadow-sm border-0 rounded-4 pos-product-card" onclick="addToCart(<?= $jsonStr ?>)">
                        <div style="height: 140px; overflow: hidden; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                            <img src="<?= $gb ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                        </div>
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark text-truncate mb-1" title="<?= htmlspecialchars($p['nama_produk']) ?>"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-success fw-bold">Rp <?= number_format($p['harga_jual'],0,',','.') ?></span>
                                <span class="badge bg-light text-dark border">Stok: <?= $p['stok'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div id="pencarianKosong" class="text-center py-5 d-none">
                <i class="bi bi-bag-x fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted fw-bold">Barang tidak ditemukan di rak / stok habis.</h5>
            </div>
        </div>
        <div class="col-lg-4 pos-sidebar-right">
            <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center rounded-4">
                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-cart3 me-1"></i> Keranjang Nota</h6>
                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearCart()"><i class="bi bi-trash"></i> Reset</button>
                </div>
                <div class="card-body p-0 cart-wrapper px-2" id="cartContainer">
                    <div id="cartEmptyState" class="text-center py-5 mt-4">
                        <i class="bi bi-bag text-light d-block mb-3" style="font-size:3rem"></i>
                        <span class="text-muted fw-medium small">Belum ada barang dipilih...</span>
                    </div>
                </div>
                <div class="p-4 bg-light" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Subtotal Harga</span>
                        <span id="txtSubtotal" class="fw-bold text-dark">Rp 0</span>
                    </div>
                    <div class="row gx-2 mb-2 align-items-center">
                        <div class="col-6">
                            <span class="small text-muted">Diskon (Rp)</span>
                            <input type="number" id="inpDiskon" class="form-control form-control-sm text-end mt-1 rounded-3" value="0" min="0">
                        </div>
                        <div class="col-6">
                            <span class="small text-muted">Pajak / PPN (%)</span>
                            <div class="input-group input-group-sm mt-1">
                                <input type="number" id="inpPajak" class="form-control text-end border-end-0 rounded-start-3" value="0" min="0" max="100">
                                <span class="input-group-text bg-white rounded-end-3">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="pos-summary pb-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h5 class="fw-bold text-primary m-0">Grand Total</h5>
                            <h3 class="fw-bold text-success m-0" id="txtTotalBayar">Rp 0</h3>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center fs-5" id="btnProsesBayar" data-bs-toggle="modal" data-bs-target="#modalPembayaran">
                        <i class="bi bi-wallet2 me-2"></i> Bayar & Cetak Struk
                    </button>
                    <input type="hidden" id="csrfs" value="<?= htmlspecialchars($csrfToken) ?>">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalPembayaran" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow border-0" style="border-radius:20px;">
      <div class="modal-header border-bottom-0 p-4 pb-0 text-center mx-auto w-100">
        <h4 class="modal-title fw-bold text-primary d-block w-100">Terima Pembayaran</h4>
      </div>
      <div class="modal-body p-4 pt-2 text-center">
        <h2 class="fw-bold text-success mb-4" id="modalFixTotal">Rp 0</h2>
        <div class="form-floating mb-3 text-start">
            <select class="form-select rounded-4 fw-medium" id="pelangganTrx">
                <option value="">Umum (Tanpa Registrasi Member)</option>
                <?php foreach($pelanggan as $pl): ?>
                <option value="<?= $pl['id_pelanggan'] ?>"><?= htmlspecialchars($pl['nama_pelanggan']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Peng-order (Customer)</label>
        </div>
        <div class="form-floating mb-3">
            <input type="number" class="form-control rounded-4 text-center fs-4 fw-bold text-primary" id="uangBayarInp" placeholder="0" autofocus>
            <label>Uang Tunai Cashier (Rp)</label>
        </div>
        <div class="border text-center rounded-4 p-3 bg-light d-flex justify-content-between align-items-center">
            <span class="text-secondary fw-medium">Uang Kembali:</span>
            <span class="fs-4 fw-bold text-dark" id="kembalianFix">Rp 0</span>
        </div>
        <div class="d-flex mt-4 gap-2">
            <button class="btn btn-light rounded-pill py-3 fw-medium w-50" data-bs-dismiss="modal">Tutup (Edit)</button>
            <button class="btn btn-success rounded-pill py-3 fw-bold shadow-sm w-50 disabled" id="btnSubmitTrx" onclick="submitPOS()"><i class="bi bi-printer me-1"></i> Konfirmasi Transaksi</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once 'views/layouts/footer.php'; ?>
<script>
let cart = [];
let calculation = { subtotal: 0, diskon: 0, pajak: 0, total: 0, uangBayar: 0, kembalian: 0 };
function getBarcodeDataMap() {
    let map = {};
    $('.p-item').each(function() {
         let btn = $(this).find('.pos-product-card');
         // Extract JSON from onclick attribute Hacky but fast
         let clickFn = btn.attr('onclick');
         let jsonStr = clickFn.replace('addToCart(','').replace(')','');
         map[$(this).data('barcode')] = JSON.parse(jsonStr);
    });
    return map;
}
let barcodeCache = getBarcodeDataMap();
// Keybinding F2 untuk fokus barcode scanner
$(document).on('keydown', function(e) {
    if(e.key === 'F2') {
        e.preventDefault(); $('#inputPencarian').focus();
    }
});
// Live Search Logika Cepat
$('#inputPencarian').on('input', function() {
    let q = $(this).val().toLowerCase().trim();
    let count = 0;
    // Barcode Scanner Mode (Jika input cepat secara instan panjang)
    // Scanner menekan "Enter" di belakang angka
});
$('#inputPencarian').on('keyup', function(e) {
    if(e.key === 'Enter') {
        let b = $(this).val().toLowerCase().trim();
        if(b && barcodeCache[b]) {
            addToCart(barcodeCache[b]);
            $(this).val('');
            renderCart();
            return;
        } else {
             Swal.fire({toast:true, position:'top-end', icon:'error', title:'Barcode ID tidak dikenali dlm database stok.', showConfirmButton:false, timer:2000});
             $(this).val('');
        }
    } else {
        // Normal Search Filter
        let q = $(this).val().toLowerCase().trim();
        let match = 0;
        $('.p-item').each(function() {
            let nm = String($(this).data('nama'));
            let sku = String($(this).data('sku'));
            let bc = String($(this).data('barcode'));
            if(nm.includes(q) || sku.includes(q) || bc.includes(q)) { $(this).removeClass('d-none'); match++; }
            else { $(this).addClass('d-none'); }
        });
        if(match===0) $('#pencarianKosong').removeClass('d-none'); else $('#pencarianKosong').addClass('d-none');
    }
});
function addToCart(item) {
    let existing = cart.find(x => x.id_produk === item.id_produk);
    if(existing) {
        if(existing.qty >= item.stok) {
            Swal.fire({toast:true, position:'top-end', icon:'warning', title:'Stok Rak (Fisik) Terlampaui!', showConfirmButton:false, timer:1500});
            return;
        }
        existing.qty += 1;
    } else {
        cart.push({ id_produk: item.id_produk, nama: item.nama_produk, harga: parseFloat(item.harga_jual), qty: 1, stok: parseInt(item.stok) });
    }
    renderCart();
}
function minCart(id) {
    let idx = cart.findIndex(x => x.id_produk === id);
    if(idx > -1) {
        if(cart[idx].qty > 1) cart[idx].qty -= 1;
        else cart.splice(idx, 1);
        renderCart();
    }
}
function maxCart(id) {
    let idx = cart.findIndex(x => x.id_produk === id);
    if(idx > -1) {
        if(cart[idx].qty < cart[idx].stok) cart[idx].qty += 1;
        else Swal.fire({toast:true, position:'top-end', icon:'error', title:'Mentok Limit Stok', showConfirmButton:false, timer:1000});
        renderCart();
    }
}
function clearCart() {
    if(cart.length===0) return;
    Swal.fire({title:'Hapus Transisi?', icon:'warning', showCancelButton:true, confirmButtonColor:'#e11d48'}).then((r)=>{
        if(r.isConfirmed) { cart = []; renderCart(); }
    });
}
function calcRekap() {
    let sub = 0;
    cart.forEach(c => sub += (c.qty * c.harga));
    let disC = parseFloat($('#inpDiskon').val())||0;
    let pajC = parseFloat($('#inpPajak').val())||0;
    let taxAmt = (sub - disC) * (pajC/100);
    let gTot = (sub - disC) + taxAmt;
    if(gTot < 0) gTot = 0;
    calculation.subtotal = sub;
    calculation.diskon = disC;
    calculation.pajak = taxAmt;
    calculation.total = gTot;
    $('#txtSubtotal').text('Rp ' + sub.toLocaleString('id-ID'));
    $('#txtTotalBayar').text('Rp ' + gTot.toLocaleString('id-ID'));
    $('#modalFixTotal').text('Rp ' + gTot.toLocaleString('id-ID'));
    recalcBayar();
}
$('#inpDiskon, #inpPajak').on('input', function() { calcRekap(); });
function renderCart() {
    let $c = $('#cartContainer');
    $c.find('.c-item').remove();
    if(cart.length === 0) {
        $('#cartEmptyState').show();
        $('#btnProsesBayar').addClass('disabled');
    } else {
        $('#cartEmptyState').hide();
        $('#btnProsesBayar').removeClass('disabled');
        cart.forEach(item => {
            let subt = item.harga * item.qty;
            let ht = `
            <div class="c-item p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <div style="width: 55%">
                    <div class="fw-bold text-dark text-truncate" title="${item.nama}">${item.nama}</div>
                    <div class="text-primary small fw-medium mt-1">Rp ${item.harga.toLocaleString('id-ID')} /pc</div>
                </div>
                <div class="d-flex align-items-center bg-light rounded-pill px-2 border" style="width: 100px; justify-content:space-between">
                     <button class="btn btn-sm btn-link text-danger p-1 border-0 fw-bold" onclick="minCart(${item.id_produk})" style="text-decoration:none">-</button>
                     <span class="fw-bold text-dark fs-6">${item.qty}</span>
                     <button class="btn btn-sm btn-link text-success p-1 border-0 fw-bold" onclick="maxCart(${item.id_produk})" style="text-decoration:none">+</button>
                </div>
            </div>`;
            $c.append(ht);
        });
    }
    calcRekap();
}
// Logic modal BAYAR Kasir
$('#uangBayarInp').on('input', function() { recalcBayar(); });
$('#modalPembayaran').on('shown.bs.modal', function () { $('#uangBayarInp').focus(); });
function recalcBayar() {
    let myUang = parseFloat($('#uangBayarInp').val()) || 0;
    calculation.uangBayar = myUang;
    let k = myUang - calculation.total;
    calculation.kembalian = k;
    if(k < 0) {
        $('#kembalianFix').text('- Rp ' + Math.abs(k).toLocaleString('id-ID')).addClass('text-danger').removeClass('text-dark text-success');
        $('#btnSubmitTrx').addClass('disabled');
    } else {
        $('#kembalianFix').text('Rp ' + k.toLocaleString('id-ID')).addClass('text-success').removeClass('text-danger text-dark');
        $('#btnSubmitTrx').removeClass('disabled');
    }
}
function submitPOS() {
    let tk = $('#csrfs').val();
    let pln = $('#pelangganTrx').val();
    let dtx = {
        action: 'checkout',
        csrf_token: tk,
        id_pelanggan: pln,
        keranjang: JSON.stringify(cart),
        subtotal: calculation.subtotal,
        diskon: $('#inpDiskon').val()||0,
        pajak: $('#inpPajak').val()||0, 
        pajak_val: calculation.pajak,
        total_bayar: calculation.total,
        uang_bayar: calculation.uangBayar,
        kembalian: calculation.kembalian
    };
    let bc = $('#btnSubmitTrx'); let ab = bc.html();
    bc.prop('disabled', true).text('Menyimpan Nota...');
    $.post('controllers/PosController.php', dtx, function(r) {
         if(r.sukses) {
             Swal.fire({
                 icon: 'success', title: 'Transaksi Lunas Tercatat!',
                 text: r.pesan + " Membuka halaman Struk...", timer: 2000, showConfirmButton: false
             }).then(() => {
                 window.open('cetak_struk.php?id=' + r.data_id, '_blank');
                 window.location.reload(); // reset memory aplikasi penuh
             });
         } else {
             Swal.fire('Error Database', r.pesan, 'error');
             bc.prop('disabled', false).html(ab);
         }
    }, 'json').fail(function(){ Swal.fire('Error', 'Proses Post Checkout HTTP Fail', 'error'); bc.prop('disabled', false).html(ab); });
}
</script>
