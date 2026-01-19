<?php

// --- MANUAL CORS HANDLING (FAIL-SAFE) ---
$allowedOrigins = [
    'https://frontend-perpus-nu.vercel.app',
    'http://localhost:3000',
    'http://127.0.0.1:8000'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Allow any Vercel preview URL or specific allowed origins
if (in_array($origin, $allowedOrigins) || preg_match('/^https:\/\/frontend-perpus-.*\.vercel\.app$/', $origin)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    // Default fallback for other domains (optional, remove if strict security needed)
    // header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning, Accept");

// Handle preflight requests immediately
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
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
