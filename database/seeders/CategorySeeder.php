<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Alat Bantu Jalan',     'slug' => 'alat-bantu-jalan'],
            ['name' => 'Monitoring Kesehatan',  'slug' => 'monitoring-kesehatan'],
            ['name' => 'Alat Pernapasan',       'slug' => 'alat-pernapasan'],
            ['name' => 'Suplemen & Obat Luar',  'slug' => 'suplemen-obat-luar'],
            ['name' => 'Perawatan Luka',        'slug' => 'perawatan-luka'],
            ['name' => 'Lainnya',               'slug' => 'lainnya'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
