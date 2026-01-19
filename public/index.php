<?php

// --- DEBUG & CORS PRE-FLIGHT ---
// Log request to stderr for Railway logs
file_put_contents('php://stderr', sprintf(
    "[%s] %s %s Origin: %s\n",
    date('c'),
    $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
    $_SERVER['HTTP_ORIGIN'] ?? 'NONE'
));

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = [
    'https://frontend-perpus-nu.vercel.app',
    'http://localhost:3000',
    'http://localhost:8000'
];

if (in_array($origin, $allowed_origins) || empty($origin)) {
    // If valid origin (or no origin/server-to-server), allow it
    // Note: 'empty($origin)' check allows non-browser tools (like Postman) to work if they don't send Origin
    header("Access-Control-Allow-Origin: " . ($origin ?: '*'));
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With, ngrok-skip-browser-warning");
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// -------------------------------

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
try {
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // FATAL ERROR HANDLER WITH CORS
    // Jika Laravel crash (misal DB connect error), tangkap di sini dan kirim JSON + CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With, ngrok-skip-browser-warning");
    header('Content-Type: application/json');
    http_response_code(500);
    
    echo json_encode([
        'message' => 'Server Error (Captured by index.php)',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    exit();
}
