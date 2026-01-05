<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $ring = Category::where('name','Ring')->first();
        $coin = Category::where('name','Coin')->first();
        Product::create([
    'name' => 'Gold Ring 22K',
    'category_id' => $ring->id,
    'metal' => 'Gold',
    'purity' => '22K',
    'weight' => 50
]);

Product::create([
    'name' => 'Silver Coin',
    'category_id' => $coin->id,
    'metal' => 'Silver',
    'purity' => '99.9',
    'weight' => 1000
]);
        // $products = [
        //     [
        //         'name' => 'Gold Ring 22K',
        //         'category' => 'Ring',
        //         'metal' => 'Gold',
        //         'purity' => '22K',
        //         'weight' => 50.000
        //     ],
        //     [
        //         'name' => 'Gold Chain 22K',
        //         'category' => 'Chain',
        //         'metal' => 'Gold',
        //         'purity' => '22K',
        //         'weight' => 120.500
        //     ],
        //     [
        //         'name' => 'Gold Necklace 22K',
        //         'category' => 'Necklace',
        //         'metal' => 'Gold',
        //         'purity' => '22K',
        //         'weight' => 200.000
        //     ],
        //     [
        //         'name' => 'Silver Anklet',
        //         'category' => 'Anklet',
        //         'metal' => 'Silver',
        //         'purity' => '92.5',
        //         'weight' => 300.750
        //     ],
        //     [
        //         'name' => 'Silver Coin',
        //         'category' => 'Coin',
        //         'metal' => 'Silver',
        //         'purity' => '99.9',
        //         'weight' => 1000.000
        //     ]
        // ];

        // foreach ($products as $product) {
        //     Product::create($product);
        // }
    }
}
