<?php

/**
 * Dayana Enterprises ERP - Database Setup Script
 * Run: php setup_database.php
 */

echo "Dayana Enterprises ERP Database Setup\n";
echo "==================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die("Error: Run this script from the Laravel project root.\n");
}

echo "[1/4] Running migrations...\n";
$output = shell_exec('php artisan migrate:fresh --force 2>&1');
echo $output;

echo "\n[2/4] Seeding roles and permissions...\n";
$output = shell_exec('php artisan db:seed --class=RolePermissionSeeder --force 2>&1');
echo $output;

echo "\n[3/4] Seeding users...\n";
$output = shell_exec('php artisan db:seed --class=UserSeeder --force 2>&1');
echo $output;

echo "\n[4/4] Seeding master data...\n";
$output = shell_exec('php artisan db:seed --class=DatabaseSeeder --force 2>&1');
echo $output;

echo "\n==================================\n";
echo "Setup Complete!\n";
echo "==================================\n\n";
echo "Login Credentials:\n";
echo "  Super Admin: admin@dayana.com / password\n";
echo "  Manager: manager@dayana.com / password\n";
echo "  Cashier: cashier@dayana.com / password\n";
echo "  Store Keeper: store@dayana.com / password\n";
echo "  Accountant: accountant@dayana.com / password\n";