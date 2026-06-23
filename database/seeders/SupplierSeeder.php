<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_code' => 'SUP-001',
                'supplier_name' => 'Juki Lanka (Pvt) Ltd',
                'address' => 'No. 88, Industry Road, Ekala, Ja-Ela',
                'contact_person' => 'Mr. Raj Patel',
                'phone' => '0112233445',
                'email' => 'sales@jukilanka.lk',
                'credit_limit' => 2000000,
                'current_balance' => 450000,
                'is_active' => true,
            ],
            [
                'supplier_code' => 'SUP-002',
                'supplier_name' => 'Singer Sri Lanka',
                'address' => 'No. 234, Union Place, Colombo 02',
                'contact_person' => 'Ms. Priya Fernando',
                'phone' => '0113344556',
                'email' => 'b2b@singer.lk',
                'credit_limit' => 1500000,
                'current_balance' => 275000,
                'is_active' => true,
            ],
            [
                'supplier_code' => 'SUP-003',
                'supplier_name' => 'Brother International Singapore',
                'address' => 'Industrial Complex, Singapore',
                'contact_person' => 'Mr. Tan Wei Ming',
                'phone' => '+65 6789 0123',
                'email' => 'sales@brother.sg',
                'credit_limit' => 3000000,
                'current_balance' => 890000,
                'is_active' => true,
            ],
            [
                'supplier_code' => 'SUP-004',
                'supplier_name' => 'Local Sewing Parts Distributor',
                'address' => 'No. 67, Silver Street, Pettah, Colombo 11',
                'contact_person' => 'Mr. Kumar Sangakkara',
                'phone' => '0114455667',
                'email' => 'parts@sewingparts.lk',
                'credit_limit' => 500000,
                'current_balance' => 95000,
                'is_active' => true,
            ],
            [
                'supplier_code' => 'SUP-005',
                'supplier_name' => 'Gunold Threads India',
                'address' => 'Textile Park, Chennai, India',
                'contact_person' => 'Mr. Arun Kumar',
                'phone' => '+91 44 2345 6789',
                'email' => 'orders@gunold.in',
                'credit_limit' => 1000000,
                'current_balance' => 180000,
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}