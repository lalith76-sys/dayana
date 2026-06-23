<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@dayana.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '0112345678',
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@dayana.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'phone' => '0112345679',
                'is_active' => true,
            ]
        );
        $manager->assignRole('Manager');

        // Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@dayana.com'],
            [
                'name' => 'Cashier',
                'password' => Hash::make('password'),
                'phone' => '0112345680',
                'is_active' => true,
            ]
        );
        $cashier->assignRole('Cashier');

        // Store Keeper
        $storeKeeper = User::firstOrCreate(
            ['email' => 'store@dayana.com'],
            [
                'name' => 'Store Keeper',
                'password' => Hash::make('password'),
                'phone' => '0112345681',
                'is_active' => true,
            ]
        );
        $storeKeeper->assignRole('Store Keeper');

        // Accountant
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@dayana.com'],
            [
                'name' => 'Accountant',
                'password' => Hash::make('password'),
                'phone' => '0112345682',
                'is_active' => true,
            ]
        );
        $accountant->assignRole('Accountant');
    }
}