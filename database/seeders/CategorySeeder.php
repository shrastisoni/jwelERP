<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Ring'],
            ['name' => 'Chain'],
            ['name' => 'Necklace'],
            ['name' => 'Bangle'],
            ['name' => 'Bracelet'],
            ['name' => 'Earring'],
            ['name' => 'Anklet'],
            ['name' => 'Pendant'],
            ['name' => 'Coin'],
            ['name' => 'Mangalsutra'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
