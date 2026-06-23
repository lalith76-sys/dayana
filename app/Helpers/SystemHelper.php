<?php

namespace App\Helpers;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;

class SystemHelper
{
    public static function generateCode($prefix, $model, $field = 'id', $length = 6)
    {
        $lastRecord = $model::withTrashed()->latest('id')->first();
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $number = str_pad($lastId + 1, $length, '0', STR_PAD_LEFT);
        return $prefix . $number;
    }

    public static function getSetting($key, $default = null)
    {
        $setting = GeneralSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function formatCurrency($amount)
    {
        return 'Rs. ' . number_format($amount, 2);
    }

    public static function calculateProfit($sellingPrice, $costPrice, $quantity = 1)
    {
        return ($sellingPrice - $costPrice) * $quantity;
    }

    public static function getDashboardStats()
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();

        return [
            'today_sales' => DB::table('sales_invoices')
                ->whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total'),
            'monthly_sales' => DB::table('sales_invoices')
                ->whereDate('created_at', '>=', $startOfMonth)
                ->where('status', 'completed')
                ->sum('total'),
            'customer_receivables' => DB::table('customers')->sum('current_balance'),
            'supplier_payables' => DB::table('suppliers')->sum('current_balance'),
            'stock_value' => DB::table('items')
                ->select(DB::raw('SUM(stock_quantity * cost_price) as total'))
                ->value('total'),
            'low_stock_count' => DB::table('items')
                ->whereColumn('stock_quantity', '<=', 'reorder_level')
                ->count(),
            'pending_cheques' => DB::table('cheques')
                ->where('status', 'pending')
                ->sum('amount'),
            'monthly_profit' => DB::table('profit_analyses')
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->value('net_profit'),
        ];
    }

    public static function getMenuItems()
    {
        return [
            'dashboard' => [
                'title' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'dashboard',
            ],
            'masters' => [
                'title' => 'Masters',
                'icon' => 'fas fa-database',
                'submenu' => [
                    ['title' => 'Items', 'route' => 'items.index', 'icon' => 'fas fa-box'],
                    ['title' => 'Categories', 'route' => 'categories.index', 'icon' => 'fas fa-tags'],
                    ['title' => 'Suppliers', 'route' => 'suppliers.index', 'icon' => 'fas fa-truck'],
                    ['title' => 'Customers', 'route' => 'customers.index', 'icon' => 'fas fa-users'],
                ]
            ],
            'inventory' => [
                'title' => 'Inventory',
                'icon' => 'fas fa-warehouse',
                'submenu' => [
                    ['title' => 'Stock', 'route' => 'stock.index', 'icon' => 'fas fa-cubes'],
                    ['title' => 'GRN', 'route' => 'grn.index', 'icon' => 'fas fa-clipboard-check'],
                    ['title' => 'Stock Adjustments', 'route' => 'stock-adjustments.index', 'icon' => 'fas fa-exchange-alt'],
                ]
            ],
            'purchasing' => [
                'title' => 'Purchasing',
                'icon' => 'fas fa-shopping-cart',
                'submenu' => [
                    ['title' => 'Purchase Orders', 'route' => 'purchase-orders.index', 'icon' => 'fas fa-file-invoice'],
                    ['title' => 'Supplier Payments', 'route' => 'supplier-payments.index', 'icon' => 'fas fa-money-bill'],
                ]
            ],
            'sales' => [
                'title' => 'Sales',
                'icon' => 'fas fa-cash-register',
                'submenu' => [
                    ['title' => 'POS', 'route' => 'pos.index', 'icon' => 'fas fa-calculator'],
                    ['title' => 'Sales Invoices', 'route' => 'sales-invoices.index', 'icon' => 'fas fa-file-invoice-dollar'],
                    ['title' => 'Customer Payments', 'route' => 'customer-payments.index', 'icon' => 'fas fa-hand-holding-usd'],
                    ['title' => 'Sales Returns', 'route' => 'sales-returns.index', 'icon' => 'fas fa-undo'],
                ]
            ],
            'finance' => [
                'title' => 'Finance',
                'icon' => 'fas fa-chart-line',
                'submenu' => [
                    ['title' => 'Expenses', 'route' => 'expenses.index', 'icon' => 'fas fa-wallet'],
                    ['title' => 'Cheques', 'route' => 'cheques.index', 'icon' => 'fas fa-money-check'],
                    ['title' => 'Profit Analysis', 'route' => 'profit-analysis.index', 'icon' => 'fas fa-chart-pie'],
                ]
            ],
            'reports' => [
                'title' => 'Reports',
                'icon' => 'fas fa-print',
                'submenu' => [
                    ['title' => 'Inventory Reports', 'route' => 'reports.inventory', 'icon' => 'fas fa-cubes'],
                    ['title' => 'Sales Reports', 'route' => 'reports.sales', 'icon' => 'fas fa-chart-bar'],
                    ['title' => 'Purchase Reports', 'route' => 'reports.purchases', 'icon' => 'fas fa-shopping-bag'],
                    ['title' => 'Financial Reports', 'route' => 'reports.financial', 'icon' => 'fas fa-file-invoice-dollar'],
                ]
            ],
            'administration' => [
                'title' => 'Administration',
                'icon' => 'fas fa-cog',
                'submenu' => [
                    ['title' => 'Users', 'route' => 'users.index', 'icon' => 'fas fa-user-cog'],
                    ['title' => 'Roles', 'route' => 'roles.index', 'icon' => 'fas fa-user-shield'],
                    ['title' => 'Permissions', 'route' => 'permissions.index', 'icon' => 'fas fa-key'],
                ]
            ],
        ];
    }
}