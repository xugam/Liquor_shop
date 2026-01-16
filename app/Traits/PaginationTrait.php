<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PaginationTrait
{
    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 10);

        return min(max($perPage, 1), 100); // min 1, max 100
    }
}
