<?php
function cekHakAkses(string $namaModul, string $jenisAkses = 'akses_lihat') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $hakAksesValid = false;
    if (isset($_SESSION['hak_akses'][$namaModul][$jenisAkses]) && $_SESSION['hak_akses'][$namaModul][$jenisAkses] == 1) {
        $hakAksesValid = true;
    }
    if (!$hakAksesValid) {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        if ($isAjax) {
            require_once __DIR__ . '/../helpers/response_helper.php';
            kirimResponseJson(false, "Anda tidak memiliki hak akses untuk \"$jenisAkses\" pada modul \"$namaModul\".", [], 403);
        } else {
            header("HTTP/1.1 403 Forbidden");
            echo "<!DOCTYPE html><html lang='id'><head><title>403 Forbidden</title><style>";
            echo "body { font-family: sans-serif; display:flex; height:100vh; justify-content:center; align-items:center; flex-direction:column; background:#f8d7da; color:#721c24; margin:0;}";
            echo "</style></head><body>";
            echo "<h1>403 Forbidden</h1><p>Maaf, Anda tidak memiliki hak akses ($jenisAkses) ke laman/modul $namaModul.</p>";
            echo "<a href='dashboard.php' style='color:#721c24; text-decoration:underline;'>Kembali ke Dashboard</a>";
            echo "</body></html>";
            exit;
        }
    }
}
