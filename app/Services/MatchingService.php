<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

class MatchingService
{
    /**
     * Find items in the same city/province as the recipient,
     * ordered by city match first, then province.
     *
     * @param  string $city
     * @param  string $province
     * @param  int|null $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findNearby(string $city, string $province, ?int $categoryId = null): Collection
    {
        $query = Item::active()
            ->with(['category', 'primaryImage'])
            ->where(function ($q) use ($city, $province) {
                $q->where('city', 'like', "%{$city}%")
                  ->orWhere('province', 'like', "%{$province}%");
            });

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query
            ->orderByRaw("CASE WHEN city LIKE ? THEN 0 ELSE 1 END", ["%{$city}%"])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Simple score: higher estimated_value + closer location = better match.
     */
    public function scoreItem(Item $item, string $city): int
    {
        $score = 0;
        if (stripos($item->city, $city) !== false) $score += 50;
        $score += min((int)($item->estimated_value / 10000), 50);
        return $score;
    }
}
