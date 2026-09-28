<?php

namespace App\Services;

use App\Models\Item;
use App\Models\DonationRequest;
use App\Models\ImpactStat;

class ImpactService
{
    /**
     * Recalculate and upsert today's impact snapshot.
     * Called after every completed donation.
     */
    public function refreshToday(): ImpactStat
    {
        $totalItems      = Item::where('status', 'delivered')->count();
        $totalValueIdr   = Item::where('status', 'delivered')->sum('estimated_value');
        $totalCities     = Item::where('status', 'delivered')->distinct('city')->count('city');
        $totalDonors     = Item::where('status', 'delivered')->distinct('donor_id')->count('donor_id');
        $totalRecipients = DonationRequest::where('status', 'completed')->distinct('recipient_id')->count('recipient_id');

        return ImpactStat::updateOrCreate(
            ['date' => now()->toDateString()],
            [
                'total_items'      => $totalItems,
                'total_value_idr'  => $totalValueIdr,
                'total_cities'     => $totalCities,
                'total_donors'     => $totalDonors,
                'total_recipients' => $totalRecipients,
            ]
        );
    }

    /**
     * Return the latest cumulative stats for the landing page banner.
     */
    public function getCumulativeStats(): array
    {
        return [
            'total_items'      => Item::where('status', 'delivered')->count(),
            'total_value_idr'  => Item::where('status', 'delivered')->sum('estimated_value'),
            'total_cities'     => Item::where('status', 'delivered')->distinct('city')->count('city'),
            'total_donors'     => Item::where('status', 'delivered')->distinct('donor_id')->count('donor_id'),
            'total_recipients' => DonationRequest::where('status', 'completed')->distinct('recipient_id')->count('recipient_id'),
        ];
    }
}
