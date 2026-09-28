<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImpactStat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date', 'total_items', 'total_value_idr',
        'total_cities', 'total_donors', 'total_recipients',
    ];

    protected function casts(): array
    {
        return [
            'date'            => 'date',
            'total_value_idr' => 'decimal:2',
        ];
    }
}
