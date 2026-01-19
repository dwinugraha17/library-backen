<?php

// --- MANUAL CORS HANDLING (AGGRESSIVE WILDCARD) ---
// Gunakan Wildcard '*' untuk semua origin.
// Matikan 'Access-Control-Allow-Credentials' agar '*' valid.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// Allow semua header yang mungkin dikirim frontend
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With, ngrok-skip-browser-warning");

// Handle preflight requests immediately
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    // Pastikan status 200 OK dikirim
    http_response_code(200);
    // Exit script agar Laravel tidak menimpa header ini
    exit();
}
// ----------------------------------------

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
