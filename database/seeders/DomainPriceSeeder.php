<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DomainPrice;

class DomainPriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            ['category' => 'CAT1', 'old_price' => 8000.00, 'new_price' => 7500.00],
            ['category' => 'CAT2', 'old_price' => 5000.00, 'new_price' => 4600.00],
            ['category' => 'CAT3', 'old_price' => 1000.00, 'new_price' => 950.00],
            ['category' => 'SUGGESTED', 'old_price' => 5000.00, 'new_price' => 4600.00],
        ];

        foreach ($prices as $price) {
            DomainPrice::updateOrCreate(
                ['category' => $price['category']],
                ['old_price' => $price['old_price'], 'new_price' => $price['new_price']]
            );
        }
    }
}
