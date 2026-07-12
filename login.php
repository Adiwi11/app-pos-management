<?php
require_once 'config/database.php';
require_once 'helpers/security_helper.php';
require_once 'helpers/response_helper.php';
require_once 'middleware/auth_middleware.php';
cekSudahLogin();
$csrfToken = buatCsrfToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars(getNamaToko()) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9; /* Slate 100 */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-wrapper {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); /* Soft drop shadow */
            overflow: hidden;
            display: flex;
            width: 1050px;
            max-width: 95%;
            min-height: 600px;
        }
        .login-sidebar {
            /* Premium gradient: Indigo to Cyan */
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            color: white;
            padding: 4rem;
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        /* Aesthetic shapes inside sidebar */
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        .login-sidebar h1 {
            font-weight: 700;
            font-size: 3rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .login-sidebar p {
            opacity: 0.9;
            font-size: 1.15rem;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }
        .login-form-area {
            padding: 4.5rem;
            width: 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-floating > label {
            color: #64748b; /* Slate 500 */
        }
        .form-control {
            border-radius: 14px;
            padding: 1rem 0.75rem;
            border-color: #cbd5e1; /* Slate 300 */
            background-color: #f8fafc;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
            border-color: #4f46e5;
            background-color: #ffffff;
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            border-radius: 14px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
        }
        .form-title {
            color: #1e293b; /* Slate 800 */
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-sidebar d-none d-lg-flex">
        <h1><?= htmlspecialchars(getNamaToko()) ?></h1>
        <p>Wholesale POS Management System. Sistem pintar manajemen inventori, penjualan, dan stok secara real-time dan akurat.</p>
    </div>
    <div class="login-form-area">
        <div class="text-center mb-4 d-lg-none">
            <h2 class="fw-bold" style="color: #4f46e5;"><?= htmlspecialchars(getNamaToko()) ?></h2>
        </div>
        <h3 class="fw-bold form-title mb-2">Masuk ke Akun</h3>
        <p class="text-muted mb-5">Selamat datang kembali! Silakan isi kredensial Anda.</p>
        <form id="formLogin">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="form-floating mb-4">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
            </div>
            <div class="form-floating mb-5">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="btnLogin">
                Login ke Sistem <i class="bi bi-arrow-right-short ms-2 fs-5 align-middle"></i>
            </button>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    $('#formLogin').on('submit', function(e) {
        e.preventDefault();
        // Disable button dan tampilkan loader animasi
        let $btn = $('#btnLogin');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...');
        let formData = {
            email: $('#email').val(),
            password: $('#password').val(),
            csrf_token: $('#csrf_token').val()
        };
        $.ajax({
            url: 'controllers/AuthController.php?action=login',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if(res.sukses) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.pesan,
                        timer: 1500,
                        showConfirmButton: false,
                        didClose: () => {
                            window.location.href = res.data.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.pesan,
                        confirmButtonColor: '#4f46e5'
                    });
                    // Regenerate token dari backend jika dikembalikan
                    if(res.data && res.data.csrf_token) {
                        $('#csrf_token').val(res.data.csrf_token);
                    }
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let pesan = 'Tidak dapat terhubung ke server.';
                if(xhr.responseJSON && xhr.responseJSON.pesan) {
                    pesan = xhr.responseJSON.pesan;
                    if(xhr.responseJSON.data && xhr.responseJSON.data.csrf_token) {
                        $('#csrf_token').val(xhr.responseJSON.data.csrf_token);
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: pesan,
                    confirmButtonColor: '#4f46e5'
                });
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
</body>
</html>
