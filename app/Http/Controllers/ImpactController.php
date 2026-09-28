<?php

namespace App\Http\Controllers;

use App\Services\ImpactService;

class ImpactController extends Controller
{
    public function __construct(private ImpactService $impactService) {}

    /** GET /api/impact — JSON for animated counter on landing page */
    public function stats()
    {
        return response()->json($this->impactService->getCumulativeStats());
    }
}
