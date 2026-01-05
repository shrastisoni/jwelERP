<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Party;

class PartySeeder extends Seeder
{
    public function run(): void
    {
        $parties = [
            [
                'name' => 'Customer A',
                'type' => 'customer',
                'mobile' => '9876543210',
                'address' => 'Mumbai'
            ],
            [
                'name' => 'Customer B',
                'type' => 'customer',
                'mobile' => '9123456780',
                'address' => 'Delhi'
            ],
            [
                'name' => 'Supplier Gold Pvt Ltd',
                'type' => 'supplier',
                'mobile' => '9000000001',
                'address' => 'Ahmedabad'
            ],
            [
                'name' => 'Wholesale Trader',
                'type' => 'supplier',
                'mobile' => '9000000002',
                'address' => 'Jaipur'
            ]
        ];

        foreach ($parties as $party) {
            Party::create($party);
        }
    }
}
