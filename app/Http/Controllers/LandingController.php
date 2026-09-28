<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Services\ImpactService;

class LandingController extends Controller
{
    public function __construct(private ImpactService $impactService) {}

    public function index()
    {
        $stats      = $this->impactService->getCumulativeStats();
        $categories = Category::all();
        $featured   = Item::active()
            ->with(['category', 'primaryImage', 'donor'])
            ->latest()
            ->limit(6)
            ->get();

        return view('pages.landing', compact('stats', 'categories', 'featured'));
    }
}
