<?php

return [
    'enabled' => env('SSO_ENABLED', true),
    'url' => env('SSO_URL', 'https://sso.itb.ac.id/auth'),
    'callback_url' => env('SSO_CALLBACK_URL', '/login/sso/callback'),
    'client_id' => env('SSO_CLIENT_ID', ''),
    'client_secret' => env('SSO_CLIENT_SECRET', ''),
    // Shared secret yang wajib dikirim server SSO ke endpoint callback.
    // Jika kosong, endpoint SSO callback DITUTUP agar tidak bisa dipakai
    // sebagai pintu login tanpa verifikasi (mencegah login bypass).
    'callback_token' => env('SSO_CALLBACK_TOKEN', ''),
];
