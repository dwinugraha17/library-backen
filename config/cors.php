<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    // PENTING: Jika allowed_origins adalah '*', supports_credentials harus false.
    // Jika butuh cookies/auth, kita harus spesifik domain Vercel.
    // Untuk tahap awal debug, kita matikan credentials dulu (false) agar '*' bisa jalan.
    'supports_credentials' => false, 
];