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
}
