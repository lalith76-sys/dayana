<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Industrial Sewing Machines', 'description' => 'Heavy-duty industrial sewing machines'],
            ['name' => 'Domestic Sewing Machines', 'description' => 'Home-use sewing machines'],
            ['name' => 'Overlock Machines', 'description' => 'Overlock and serger machines'],
            ['name' => 'Embroidery Machines', 'description' => 'Computerized embroidery machines'],
            ['name' => 'Spare Parts', 'description' => 'Sewing machine spare parts and components'],
            ['name' => 'Accessories', 'description' => 'Sewing accessories and attachments'],
            ['name' => 'Needles', 'description' => 'Sewing machine needles all types'],
            ['name' => 'Threads', 'description' => 'Sewing threads and bobbins'],
            ['name' => 'Oils & Lubricants', 'description' => 'Sewing machine oils and lubricants'],
            ['name' => 'Tools', 'description' => 'Repair and maintenance tools'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}