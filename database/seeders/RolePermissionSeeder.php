<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Cache table may not exist yet during fresh migrations
        }

        // Create permissions
        $permissions = [
            // Item Management
            'items.create', 'items.view', 'items.edit', 'items.delete',
            // Category Management
            'categories.create', 'categories.view', 'categories.edit', 'categories.delete',
            // Supplier Management
            'suppliers.create', 'suppliers.view', 'suppliers.edit', 'suppliers.delete',
            // Customer Management
            'customers.create', 'customers.view', 'customers.edit', 'customers.delete',
            // Purchase Orders
            'purchase-orders.create', 'purchase-orders.view', 'purchase-orders.edit', 'purchase-orders.delete',
            'purchase-orders.approve', 'purchase-orders.cancel',
            // GRN
            'grn.create', 'grn.view', 'grn.edit', 'grn.delete',
            // Sales
            'sales.create', 'sales.view', 'sales.edit', 'sales.delete',
            'pos.access',
            // Payments
            'payments.create', 'payments.view', 'payments.edit', 'payments.delete',
            // Cheques
            'cheques.create', 'cheques.view', 'cheques.edit', 'cheques.delete',
            // Returns
            'returns.create', 'returns.view', 'returns.edit', 'returns.delete',
            // Expenses
            'expenses.create', 'expenses.view', 'expenses.edit', 'expenses.delete',
            // Stock Adjustments
            'stock-adjustments.create', 'stock-adjustments.view', 'stock-adjustments.edit', 'stock-adjustments.delete',
            // Reports
            'reports.view',
            // Profit Analysis
            'profit-analysis.view',
            // User Management
            'users.create', 'users.view', 'users.edit', 'users.delete',
            // Role Management
            'roles.create', 'roles.view', 'roles.edit', 'roles.delete',
            // Dashboard
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles and assign permissions
        // Super Admin - All permissions
        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $superAdmin->givePermissionTo(Permission::all());

        // Manager - All except user management
        $manager = Role::findOrCreate('Manager', 'web');
        $manager->givePermissionTo(Permission::whereNotIn('name', [
            'users.create', 'users.view', 'users.edit', 'users.delete',
            'roles.create', 'roles.view', 'roles.edit', 'roles.delete',
        ])->get());

        // Cashier - Sales, POS, Customer Payments, Receipts
        $cashier = Role::findOrCreate('Cashier', 'web');
        $cashier->givePermissionTo([
            'pos.access',
            'sales.create', 'sales.view', 'sales.edit',
            'payments.create', 'payments.view',
            'customers.view', 'customers.create',
            'dashboard.view',
        ]);

        // Store Keeper - Inventory, GRN, Stock Adjustments
        $storeKeeper = Role::findOrCreate('Store Keeper', 'web');
        $storeKeeper->givePermissionTo([
            'items.view', 'items.create', 'items.edit',
            'categories.view',
            'grn.create', 'grn.view', 'grn.edit',
            'stock-adjustments.create', 'stock-adjustments.view',
            'purchase-orders.view',
            'dashboard.view',
            'reports.view',
        ]);

        // Accountant - Expenses, Cheques, Reports
        $accountant = Role::findOrCreate('Accountant', 'web');
        $accountant->givePermissionTo([
            'expenses.create', 'expenses.view', 'expenses.edit', 'expenses.delete',
            'cheques.create', 'cheques.view', 'cheques.edit', 'cheques.delete',
            'payments.view',
            'reports.view',
            'profit-analysis.view',
            'dashboard.view',
        ]);
    }
}