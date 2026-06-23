<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'item_code' => 'ISM-001',
                'category_id' => 1,
                'item_name' => 'Juki DDL-8700 Industrial Straight Machine',
                'brand' => 'Juki',
                'cost_price' => 85000,
                'selling_price' => 125000,
                'stock_quantity' => 15,
                'reorder_level' => 5,
                'status' => 'active',
            ],
            [
                'item_code' => 'ISM-002',
                'category_id' => 1,
                'item_name' => 'Brother S-7200A Direct Drive Machine',
                'brand' => 'Brother',
                'cost_price' => 95000,
                'selling_price' => 145000,
                'stock_quantity' => 10,
                'reorder_level' => 3,
                'status' => 'active',
            ],
            [
                'item_code' => 'DSM-001',
                'category_id' => 2,
                'item_name' => 'Singer Heavy Duty 4423',
                'brand' => 'Singer',
                'cost_price' => 25000,
                'selling_price' => 45000,
                'stock_quantity' => 25,
                'reorder_level' => 10,
                'status' => 'active',
            ],
            [
                'item_code' => 'OLM-001',
                'category_id' => 3,
                'item_name' => 'Juki MO-654DE Overlock Machine',
                'brand' => 'Juki',
                'cost_price' => 55000,
                'selling_price' => 85000,
                'stock_quantity' => 8,
                'reorder_level' => 3,
                'status' => 'active',
            ],
            [
                'item_code' => 'ACC-001',
                'category_id' => 6,
                'item_name' => 'Juki Bobbin Case (Class 15)',
                'brand' => 'Juki',
                'cost_price' => 500,
                'selling_price' => 1200,
                'stock_quantity' => 200,
                'reorder_level' => 50,
                'status' => 'active',
            ],
            [
                'item_code' => 'NDL-001',
                'category_id' => 7,
                'item_name' => 'Sewing Machine Needles Size 14 (Pack 10)',
                'brand' => 'Organ',
                'cost_price' => 350,
                'selling_price' => 850,
                'stock_quantity' => 500,
                'reorder_level' => 100,
                'status' => 'active',
            ],
            [
                'item_code' => 'THR-001',
                'category_id' => 8,
                'item_name' => 'Polyester Thread White (5000m)',
                'brand' => 'Gunold',
                'cost_price' => 450,
                'selling_price' => 1200,
                'stock_quantity' => 300,
                'reorder_level' => 100,
                'status' => 'active',
            ],
            [
                'item_code' => 'OIL-001',
                'category_id' => 9,
                'item_name' => 'Sewing Machine Oil (1 Liter)',
                'brand' => 'Singer',
                'cost_price' => 600,
                'selling_price' => 1500,
                'stock_quantity' => 100,
                'reorder_level' => 30,
                'status' => 'active',
            ],
            [
                'item_code' => 'SPR-001',
                'category_id' => 5,
                'item_name' => 'Juki Feed Dog for DDL-8700',
                'brand' => 'Juki',
                'cost_price' => 2500,
                'selling_price' => 5000,
                'stock_quantity' => 40,
                'reorder_level' => 15,
                'status' => 'active',
            ],
            [
                'item_code' => 'TL-001',
                'category_id' => 10,
                'item_name' => 'Screwdriver Set for Sewing Machines (6 pcs)',
                'brand' => 'Generic',
                'cost_price' => 800,
                'selling_price' => 2000,
                'stock_quantity' => 60,
                'reorder_level' => 20,
                'status' => 'active',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}