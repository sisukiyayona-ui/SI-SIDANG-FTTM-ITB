<?php

return [
    'enabled' => env('SSO_ENABLED', true),

    // Provider: 'azure_ad' atau 'itb_sso' (lama)
    'provider' => env('SSO_PROVIDER', 'azure_ad'),

    // ── Azure AD (Microsoft Entra ID) ──
    'azure' => [
        'client_id'     => env('SSO_AZURE_CLIENT_ID', ''),
        'client_secret' => env('SSO_AZURE_CLIENT_SECRET', ''),
        'tenant_id'     => env('SSO_AZURE_TENANT_ID', ''),
        'redirect_uri'  => env('SSO_AZURE_REDIRECT_URI', env('APP_URL') . '/login/sso/callback'),
        'logout_uri'    => env('SSO_AZURE_LOGOUT_URI', env('APP_URL') . '/login'),
    ],

    // ── Legacy ITB SSO (shared-secret) ──
    'url'             => env('SSO_URL', 'https://sso.itb.ac.id/auth'),
    'callback_url'    => env('SSO_CALLBACK_URL', '/login/sso/callback'),
    'client_id'       => env('SSO_CLIENT_ID', ''),
    'client_secret'   => env('SSO_CLIENT_SECRET', ''),
    'callback_token'  => env('SSO_CALLBACK_TOKEN', ''),
];
