<?php

// --- EMERGENCY HEARTBEAT ---
if (($_SERVER['REQUEST_URI'] ?? '') === '/cek-server') {
    die("SERVER RAILWAY HIDUP - JIKA ANDA MELIHAT INI BERARTI JALUR KONEKSI BENAR");
}

// --- CORS FOR BUILT-IN SERVER ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// -------------------------------

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
