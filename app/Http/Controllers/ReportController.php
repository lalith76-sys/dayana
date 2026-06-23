<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Expense;
use App\Models\Cheque;
use App\Models\ProfitAnalysis;
use App\Exports\InventoryReportExport;
use App\Exports\SalesReportExport;
use App\Exports\PurchaseReportExport;
use App\Exports\FinancialReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function inventory()
    {
        $items = Item::with('category')
            ->select('items.*', 
                DB::raw('(stock_quantity * cost_price) as stock_value'))
            ->get();

        $totalValue = $items->sum('stock_value');
        $totalItems = $items->count();
        $lowStockCount = $items->where('stock_quantity', '<=', 'reorder_level')->count();

        return view('reports.inventory', compact('items', 'totalValue', 'totalItems', 'lowStockCount'));
    }

    public function exportInventory($format = 'pdf')
    {
        $items = Item::with('category')->get();

        if ($format === 'excel') {
            // Will need Maatwebsite Excel export class
            return Excel::download(new InventoryReportExport($items), 'inventory-report.xlsx');
        }

        $pdf = Pdf::loadView('reports.pdf.inventory', compact('items'));
        return $pdf->download('inventory-report.pdf');
    }

    public function sales()
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $invoices = SalesInvoice::with('customer', 'items.item')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->latest()
            ->get();

        $totalSales = $invoices->sum('total');
        $totalDiscount = $invoices->sum('discount');

        return view('reports.sales', compact('invoices', 'totalSales', 'totalDiscount', 'startDate', 'endDate'));
    }

    public function exportSales($format = 'pdf')
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $invoices = SalesInvoice::with('customer', 'items.item')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        if ($format === 'excel') {
            return Excel::download(new SalesReportExport($invoices), 'sales-report.xlsx');
        }

        $pdf = Pdf::loadView('reports.pdf.sales', compact('invoices', 'startDate', 'endDate'));
        return $pdf->download('sales-report.pdf');
    }

    public function purchases()
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereBetween('date', [$startDate, $endDate])
            ->latest()
            ->get();

        $totalPurchases = $purchaseOrders->sum('total_amount');

        return view('reports.purchases', compact('purchaseOrders', 'totalPurchases', 'startDate', 'endDate'));
    }

    public function exportPurchases($format = 'pdf')
    {
        $startDate = request('start_date', now()->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $pdf = Pdf::loadView('reports.pdf.purchases', compact('purchaseOrders', 'startDate', 'endDate'));
        return $pdf->download('purchases-report.pdf');
    }

    public function financial()
    {
        $profitData = ProfitAnalysis::where('year', now()->year)
            ->orderBy('month')
            ->get();

        $expenses = Expense::with('expenseType')
            ->whereYear('expense_date', now()->year)
            ->select('expense_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_type_id')
            ->get();

        $cheques = Cheque::where('status', 'pending')
            ->latest()
            ->get();

        $receivables = Customer::where('current_balance', '>', 0)->sum('current_balance');
        $payables = Supplier::where('current_balance', '>', 0)->sum('current_balance');

        return view('reports.financial', compact('profitData', 'expenses', 'cheques', 'receivables', 'payables'));
    }

    public function exportFinancial($format = 'pdf')
    {
        $profitData = ProfitAnalysis::where('year', now()->year)->orderBy('month')->get();
        $expenses = Expense::with('expenseType')->whereYear('expense_date', now()->year)
            ->select('expense_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_type_id')->get();

        $pdf = Pdf::loadView('reports.pdf.financial', compact('profitData', 'expenses'));
        return $pdf->download('financial-report.pdf');
    }
}