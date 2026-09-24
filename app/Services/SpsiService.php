<?php

namespace App\Services;

class SpsiService
{
    public const BASE_URL = 'https://spsi.itb.ac.id/nic/rest/metabase/dataset/';

    public const API_KEY = 'FTTM-bBw3Z7wTw1jQpBU8';

    public static function fetch(string $dataset): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::BASE_URL . $dataset,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => ['API-KEY: ' . self::API_KEY],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new \RuntimeException('Gagal menghubungi SPSI: ' . $error);
        }

        $data = json_decode($response, true);

        if (!is_array($data) || empty($data['status'])) {
            throw new \RuntimeException('Respons SPSI tidak valid.');
        }

        return $data['data']['items'] ?? [];
    }

    public const ITB_ACCOUNT_URL = 'https://spsi.itb.ac.id/nic/rest/sso/itbaccount';

    /**
     * Lookup akun ITB by nip / nim / akun (username INA).
     * Header boleh salah satu: nip, nim, atau akun.
     */
    public static function lookupItbAccount(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        $headerKeys = [];
        if (preg_match('/^[0-9]+$/', $query)) {
            // NIM biasanya 8 digit; NIP 18 digit — coba nim dulu utk angka pendek
            $headerKeys = strlen($query) <= 10 ? ['nim', 'nip'] : ['nip', 'nim'];
        } else {
            $headerKeys = ['akun', 'nip', 'nim'];
        }

        return self::requestItbAccountParallel($query, $headerKeys);
    }

    private static function requestItbAccountParallel(string $query, array $headerKeys): ?array
    {
        $mh = curl_multi_init();
        $handles = [];

        foreach ($headerKeys as $headerKey) {
            $headers = ['API-KEY: ' . self::API_KEY, $headerKey . ': ' . $query];
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::ITB_ACCOUNT_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$headerKey] = $ch;
        }

        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh, 0.2);
            }
        } while ($active && $status === CURLM_OK);

        $result = null;
        foreach ($handles as $ch) {
            $response = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
            if ($result === null && is_string($response) && $response !== '') {
                $data = json_decode($response, true);
                if (is_array($data) && !empty($data['status']) && !empty($data['data']) && is_array($data['data'])) {
                    $result = $data['data'];
                }
            }
            curl_close($ch);
        }
        curl_multi_close($mh);

        return $result;
    }

    /**
     * Detail mahasiswa by NIM (metabase mhs_detail).
     */
    public static function lookupMhsDetail(string $nim): ?array
    {
        $nim = trim($nim);
        if ($nim === '' || !preg_match('/^[0-9]+$/', $nim)) {
            return null;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::BASE_URL . 'mhs_detail?nim=' . urlencode($nim),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => ['API-KEY: ' . self::API_KEY],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error || !is_string($response) || $response === '') {
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['status'])) {
            return null;
        }

        return $data['data']['items'][0] ?? null;
    }
}
