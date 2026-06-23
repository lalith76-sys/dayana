<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Cheque;
use App\Models\Expense;
use App\Models\ProfitAnalysis;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        // Today's Sales
        $todaySales = SalesInvoice::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total');

        // Monthly Sales
        $monthlySales = SalesInvoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->sum('total');

        // Outstanding Receivables
        $outstandingReceivables = Customer::sum('current_balance');

        // Outstanding Payables
        $outstandingPayables = Supplier::sum('current_balance');

        // Stock Value
        $stockValue = Item::select(DB::raw('SUM(stock_quantity * cost_price) as total'))->value('total') ?? 0;

        // Low Stock Items Count
        $lowStockCount = Item::whereColumn('stock_quantity', '<=', 'reorder_level')->count();

        // Pending Cheques
        $pendingCheques = Cheque::where('status', 'pending')->sum('amount');

        // Monthly Profit
        $monthlyProfit = ProfitAnalysis::where('year', now()->year)
            ->where('month', now()->month)
            ->value('net_profit') ?? 0;

        // Charts Data
        $monthlySalesData = SalesInvoice::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total')
        )
        ->whereYear('created_at', now()->year)
        ->where('status', 'completed')
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('total', 'month');

        $months = [];
        $salesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $salesData[] = (float)($monthlySalesData[$i] ?? 0);
        }

        // Profit Trend
        $profitData = ProfitAnalysis::where('year', now()->year)
            ->orderBy('month')
            ->pluck('net_profit', 'month');
        
        $profitTrend = [];
        for ($i = 1; $i <= 12; $i++) {
            $profitTrend[] = (float)($profitData[$i] ?? 0);
        }

        // Top Selling Items
        $topSellingItems = DB::table('sales_invoice_items')
            ->join('items', 'sales_invoice_items.item_id', '=', 'items.id')
            ->select('items.item_name', DB::raw('SUM(sales_invoice_items.quantity) as total_qty'), DB::raw('SUM(sales_invoice_items.total) as total_amount'))
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereYear('sales_invoices.created_at', now()->year)
            ->where('sales_invoices.status', 'completed')
            ->groupBy('items.id', 'items.item_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Expense Analysis
        $expenseData = Expense::select(
            'expense_type_id',
            DB::raw('SUM(amount) as total')
        )
        ->whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->groupBy('expense_type_id')
        ->with('expenseType')
        ->get();

        // Recent Transactions
        $recentTransactions = SalesInvoice::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'todaySales' => $todaySales,
                'monthlySales' => $monthlySales,
                'outstandingReceivables' => $outstandingReceivables,
                'outstandingPayables' => $outstandingPayables,
                'stockValue' => $stockValue,
                'lowStockCount' => $lowStockCount,
                'pendingCheques' => $pendingCheques,
                'monthlyProfit' => $monthlyProfit,
                'chartLabels' => $months,
                'salesData' => $salesData,
                'profitTrend' => $profitTrend,
                'topSellingItems' => $topSellingItems,
                'expenseData' => $expenseData,
            ]);
        }

        return view('dashboard.index', compact(
            'todaySales', 'monthlySales', 'outstandingReceivables',
            'outstandingPayables', 'stockValue', 'lowStockCount',
            'pendingCheques', 'monthlyProfit', 'months', 'salesData',
            'profitTrend', 'topSellingItems', 'expenseData', 'recentTransactions'
        ));
    }
}