<?php

namespace App\Data;

class DummyProdi
{
    public static function all(): array
    {
        return [
            ['id' => 1, 'kode' => '322', 'nama' => 'Teknik Perminyakan', 'status' => 'Aktif'],
            ['id' => 2, 'kode' => '328', 'nama' => 'Teknik Metalurgi', 'status' => 'Aktif'],
            ['id' => 3, 'kode' => '329', 'nama' => 'Teknik Geologi', 'status' => 'Aktif'],
            ['id' => 4, 'kode' => '330', 'nama' => 'Teknik Geofisika', 'status' => 'Aktif'],
        ];
    }

    public static function find($id): ?array
    {
        foreach (self::all() as $prodi) {
            if ($prodi['id'] === $id) return $prodi;
        }
        return null;
    }
}
