<?php

namespace App\Services;

class OneAppService
{
    public const DOSEN_TENDIK_FTTM_URL = 'https://oneapp.itb.ac.id/api/v1/data-master/public/data-dosen-tendik-fttm';

    public const API_TOKEN = 'odm_9f98077197ec392014658f0658a82d94aaa6ad4229e59d6e';

    /**
     * Ambil data dosen & tendik FTTM dari oneapp ITB (method POST).
     * Mengembalikan array of ['nip', 'nama', 'gelar_depan', 'gelar_belakang', 'kategori', 'sumber_data'].
     */
    public static function fetchDosenTendikFttm(): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::DOSEN_TENDIK_FTTM_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{}',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . self::API_TOKEN,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            throw new \RuntimeException('Gagal menghubungi oneapp ITB: ' . $error);
        }

        $data = json_decode((string) $response, true);

        if ($status < 200 || $status >= 300 || !is_array($data) || empty($data['success']) || !isset($data['data']) || !is_array($data['data'])) {
            throw new \RuntimeException('Respons oneapp ITB tidak valid (HTTP ' . $status . ').');
        }

        return $data['data'];
    }

    /**
     * Susun nama tampil: gelar_depan + nama + gelar_belakang.
     */
    public static function formatNama(array $record): string
    {
        $parts = [
            trim((string) ($record['gelar_depan'] ?? '')),
            trim((string) ($record['nama'] ?? '')),
            trim((string) ($record['gelar_belakang'] ?? '')),
        ];
        $parts = array_filter($parts, fn ($p) => $p !== '');

        return trim(preg_replace('/\s+/', ' ', implode(' ', $parts)));
    }
}
