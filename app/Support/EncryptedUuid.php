<?php

namespace App\Support;

class EncryptedUuid
{
    public static function encode(int $id): string
    {
        $hex = bin2hex(openssl_encrypt((string) $id, 'AES-256-ECB', self::key(), OPENSSL_RAW_DATA));
        $hex = str_pad($hex, 32, '0', STR_PAD_LEFT);

        return implode('-', [
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        ]);
    }

    public static function decode(string $uuid): ?int
    {
        $uuid = strtolower(trim($uuid));
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid)) {
            return null;
        }

        $cipher = @hex2bin(str_replace('-', '', $uuid));
        if ($cipher === false || $cipher === '') {
            return null;
        }

        $plain = @openssl_decrypt($cipher, 'AES-256-ECB', self::key(), OPENSSL_RAW_DATA);
        if ($plain === false || !ctype_digit($plain)) {
            return null;
        }

        return (int) $plain;
    }

    private static function key(): string
    {
        return substr(hash('sha256', (string) config('app.key'), true), 0, 32);
    }
}
