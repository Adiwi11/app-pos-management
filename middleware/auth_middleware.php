<?php
function cekBelumLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['id_pengguna'])) {
        header("Location: login.php?error=not_logged_in");
        exit;
    }
}
function cekSudahLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['id_pengguna'])) {
        header("Location: dashboard.php");
        exit;
    }
}
