<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'customer_code' => 'CUS-001',
                'customer_name' => 'Lanka Garments (Pvt) Ltd',
                'phone' => '0112456789',
                'address' => 'No. 125, Industrial Zone, Ratmalana',
                'email' => 'info@lankagarments.lk',
                'credit_limit' => 500000,
                'current_balance' => 125000,
                'is_active' => true,
            ],
            [
                'customer_code' => 'CUS-002',
                'customer_name' => 'Colombo Fashion House',
                'phone' => '0112345670',
                'address' => 'No. 45, Galle Road, Colombo 03',
                'email' => 'info@colombofashion.lk',
                'credit_limit' => 300000,
                'current_balance' => 75000,
                'is_active' => true,
            ],
            [
                'customer_code' => 'CUS-003',
                'customer_name' => 'Kandy Textile Mills',
                'phone' => '0812345678',
                'address' => 'No. 78, Peradeniya Road, Kandy',
                'email' => 'info@kandymills.lk',
                'credit_limit' => 400000,
                'current_balance' => 50000,
                'is_active' => true,
            ],
            [
                'customer_code' => 'CUS-004',
                'customer_name' => 'Galle Tailoring Center',
                'phone' => '0912345678',
                'address' => 'No. 12, Main Street, Galle',
                'email' => 'galle.tailor@gmail.com',
                'credit_limit' => 100000,
                'current_balance' => 15000,
                'is_active' => true,
            ],
            [
                'customer_code' => 'CUS-005',
                'customer_name' => 'Nuwan Sewing Academy',
                'phone' => '0771234567',
                'address' => 'No. 56, High Level Road, Nugegoda',
                'email' => 'info@nuwansewing.lk',
                'credit_limit' => 200000,
                'current_balance' => 30000,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}