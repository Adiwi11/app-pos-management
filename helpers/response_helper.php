<?php
function kirimResponseJson(bool $sukses, string $pesan, array $data = [], int $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'sukses' => $sukses,
        'pesan'  => $pesan,
        'data'   => $data
    ]);
    exit;
}
function arahkanHalaman(string $url) {
    header("Location: " . $url);
    exit;
}
