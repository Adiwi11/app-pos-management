<?php
?>
    </div> 
</div> 
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Logic Layout
$(document).ready(function () {
    // Tombol Toggle Buka Tutup Sidebar
    $('#sidebarCollapse, #sidebarCloseBtn').on('click', function () {
        $('#sidebar').toggleClass('active');
        // Sesuaikan margin content apabila layar di atas tablet (desktop)
        if($(window).width() >= 992){
            $('#content').toggleClass('active');
        }
    });
    // Pada resolusi mobile, sidebar otomatis tersembunyi berkat konfigurasi CSS bawaan (tanpa class active).
    // Tidak perlu lagi addClass('active') di sini karena justru akan memunculkan sidebar menutupi layar.
    // Default datatables Indonesian Language & Responsive
    $.extend( true, $.fn.dataTable.defaults, {
        responsive: true,
        language: {
            "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing":   "Sedang memproses...",
            "sLengthMenu":   "Tampilkan _MENU_ entri",
            "sZeroRecords":  "Tidak ditemukan data yang sesuai",
            "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix":  "",
            "sSearch":       "Cari:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst":    "Pertama",
                "sPrevious": "<i class='bi bi-chevron-left'></i>",
                "sNext":     "<i class='bi bi-chevron-right'></i>",
                "sLast":     "Terakhir"
            }
        }
    } );
});
</script>
</body>
</html>
