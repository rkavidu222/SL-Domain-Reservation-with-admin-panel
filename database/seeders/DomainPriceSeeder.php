<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DomainPrice;

class DomainPriceSeeder extends Seeder
{
    public function run()
    {
        $prices = [
            ['category' => 'CAT1', 'old_price' => 5000.00, 'new_price' => 4500.00],
            ['category' => 'CAT2', 'old_price' => 3000.00, 'new_price' => 2500.00],
            ['category' => 'CAT3', 'old_price' => 1500.00, 'new_price' => 1200.00],
            ['category' => 'CAT4', 'old_price' => 8000.00, 'new_price' => 7000.00],
            ['category' => 'CAT5', 'old_price' => 10000.00, 'new_price' => 8500.00],
            ['category' => 'SUGGESTED', 'old_price' => 2000.00, 'new_price' => 1800.00],
        ];

        foreach ($prices as $price) {
            DomainPrice::updateOrCreate(
                ['category' => $price['category']],
                ['old_price' => $price['old_price'], 'new_price' => $price['new_price']]
            );
        }
    }
}
