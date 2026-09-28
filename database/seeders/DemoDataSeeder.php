<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use App\Models\DonationRequest;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──
        $admin = User::firstOrCreate(
            ['email' => 'admin@alkeslink.id'],
            ['name' => 'Admin AlkesLink', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // ── Demo Donors ──
        $donor1 = User::firstOrCreate(
            ['email' => 'budi.donor@example.com'],
            ['name' => 'Budi Santoso', 'password' => Hash::make('password'), 'role' => 'donor',
             'province' => 'Jawa Barat', 'city' => 'Bandung', 'district' => 'Cicendo']
        );
        $donor2 = User::firstOrCreate(
            ['email' => 'siti.donor@example.com'],
            ['name' => 'Siti Rahayu', 'password' => Hash::make('password'), 'role' => 'donor',
             'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Tebet']
        );

        // ── Demo Recipients ──
        $recipient1 = User::firstOrCreate(
            ['email' => 'andi.penerima@example.com'],
            ['name' => 'Andi Wijaya', 'password' => Hash::make('password'), 'role' => 'recipient',
             'province' => 'Jawa Barat', 'city' => 'Bandung', 'district' => 'Antapani']
        );
        $recipient2 = User::firstOrCreate(
            ['email' => 'dewi.penerima@example.com'],
            ['name' => 'Dewi Lestari', 'password' => Hash::make('password'), 'role' => 'recipient',
             'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Tebet']
        );

        $categories = Category::all()->keyBy('slug');

        // ── Demo Items ──
        $itemsData = [
            [
                'donor_id'        => $donor1->id,
                'category_id'     => $categories['alat-bantu-jalan']->id,
                'title'           => 'Kursi Roda Lipat Standar',
                'description'     => 'Kursi roda lipat aluminium, ringan, bekas pakai 1 tahun. Kondisi masih sangat baik, semua bagian berfungsi normal.',
                'condition'       => 'baik',
                'quantity'        => 1,
                'province'        => 'Jawa Barat',
                'city'            => 'Bandung',
                'district'        => 'Cicendo',
                'status'          => 'active',
                'verified_by'     => $admin->id,
                'verified_at'     => now(),
                'estimated_value' => 800000,
            ],
            [
                'donor_id'        => $donor2->id,
                'category_id'     => $categories['monitoring-kesehatan']->id,
                'title'           => 'Alat Cek Gula Darah (Glucometer)',
                'description'     => 'Glucometer merk Accu-Check, lengkap dengan lancing device. Sudah tidak digunakan karena kondisi pasien membaik.',
                'condition'       => 'baik',
                'quantity'        => 1,
                'province'        => 'DKI Jakarta',
                'city'            => 'Jakarta Selatan',
                'district'        => 'Tebet',
                'status'          => 'active',
                'verified_by'     => $admin->id,
                'verified_at'     => now(),
                'estimated_value' => 350000,
            ],
            [
                'donor_id'        => $donor1->id,
                'category_id'     => $categories['alat-bantu-jalan']->id,
                'title'           => 'Kruk Ketiak Aluminium (Sepasang)',
                'description'     => 'Kruk aluminium adjustable, bekas pakai pasca patah tulang. Sudah disanitasi dan siap pakai.',
                'condition'       => 'cukup',
                'quantity'        => 2,
                'province'        => 'Jawa Barat',
                'city'            => 'Bandung',
                'district'        => 'Sukajadi',
                'status'          => 'active',
                'verified_by'     => $admin->id,
                'verified_at'     => now(),
                'estimated_value' => 200000,
            ],
        ];

        foreach ($itemsData as $data) {
            $item = Item::firstOrCreate(['title' => $data['title'], 'donor_id' => $data['donor_id']], $data);
            // Add a placeholder image reference (in production, real uploads are used)
            if ($item->images()->count() === 0) {
                ItemImage::create([
                    'item_id'    => $item->id,
                    'image_url'  => 'https://placehold.co/600x400/0d9488/ffffff?text=' . urlencode($item->title),
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }
        }

        // ── Demo Request ──
        $firstItem = Item::where('status', 'active')->first();
        if ($firstItem) {
            DonationRequest::firstOrCreate(
                ['item_id' => $firstItem->id, 'recipient_id' => $recipient1->id],
                ['reason' => 'Ibu saya baru saja operasi lutut dan membutuhkan kursi roda untuk pemulihan. Kami tidak mampu membeli yang baru.', 'status' => 'pending']
            );
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Admin: admin@alkeslink.id / password');
        $this->command->info('Donor: budi.donor@example.com / password');
        $this->command->info('Penerima: andi.penerima@example.com / password');
    }
}
