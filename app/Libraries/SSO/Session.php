<?php

namespace App\Libraries\SSO;

use Illuminate\Support\Facades\Session as LaravelSession;

/**
 * SSO Session wrapper for Laravel
 */
class Session
{
    public static function set($key, $value)
    {
        LaravelSession::put('SSOITB.' . $key, $value);
    }

    public static function unset($key)
    {
        LaravelSession::forget('SSOITB.' . $key);
    }

    public static function get($key)
    {
        return LaravelSession::get('SSOITB.' . $key);
    }
}
