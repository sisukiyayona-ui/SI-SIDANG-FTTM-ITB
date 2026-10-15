<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Jumlah record per halaman (combo box: 10, 25, 50, 100).
     */
    protected function resolvePerPage(Request $request, int $default = 10): int
    {
        $perPage = (int) $request->get('per_page', $default);
        return in_array($perPage, [10, 25, 50, 100], true) ? $perPage : $default;
    }
}
