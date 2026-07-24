<?php

return [
    'enabled' => env('SSO_ENABLED', true),
    'url' => env('SSO_URL', 'https://sso.itb.ac.id/auth'),
    'callback_url' => env('SSO_CALLBACK_URL', '/login/sso/callback'),
    'client_id' => env('SSO_CLIENT_ID', ''),
    'client_secret' => env('SSO_CLIENT_SECRET', ''),
];
