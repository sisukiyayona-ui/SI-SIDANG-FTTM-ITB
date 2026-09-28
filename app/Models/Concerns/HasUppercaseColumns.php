<?php

namespace App\Models\Concerns;

trait HasUppercaseColumns
{
    public function getAttribute($key)
    {
        if ($key === 'id' || array_key_exists($key, $this->attributes)) {
            return parent::getAttribute($key);
        }

        $upper = strtoupper((string) $key);
        if (array_key_exists($upper, $this->attributes)) {
            return $this->attributes[$upper];
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if ($key === 'id') {
            return parent::setAttribute($key, $value);
        }

        $upper = strtoupper((string) $key);
        if (in_array($upper, $this->getFillable(), true)) {
            // Normalisasi nilai status: selalu simpan AKTIF / NON AKTIF (uppercase)
            // apa pun sumbernya (form manual, import Excel, sync SPSI, seeder).
            if ($upper === 'STATUS_AKTIF' && is_string($value)) {
                $value = strtoupper(trim($value));
                if ($value === 'NONAKTIF') {
                    $value = 'NON AKTIF';
                }
            }

            return parent::setAttribute($upper, $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function qualifyColumn($column)
    {
        if ($column === 'id' || str_contains((string) $column, '.')) {
            return parent::qualifyColumn($column);
        }

        return parent::qualifyColumn(strtoupper((string) $column));
    }
}
