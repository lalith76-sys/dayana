<?php

namespace App\Http\Controllers;

use App\Models\ProfitAnalysis;
use App\Models\SalesInvoice;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ProfitAnalysisController extends Controller
{
    public function index()
    {
        $year = request('year', now()->year);
        
        $profitData = ProfitAnalysis::where('year', $year)
            ->orderBy('month')
            ->get();

        // Calculate totals
        $totalSales = $profitData->sum('total_sales');
        $totalCost = $profitData->sum('total_cost');
        $grossProfit = $profitData->sum('gross_profit');
        $totalExpenses = $profitData->sum('total_expenses');
        $netProfit = $profitData->sum('net_profit');

        $months = [];
        $salesData = [];
        $costData = [];
        $profitDataArr = [];
        $expenseDataArr = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $monthData = $profitData->where('month', $i)->first();
            $salesData[] = (float)($monthData->total_sales ?? 0);
            $costData[] = (float)($monthData->total_cost ?? 0);
            $profitDataArr[] = (float)($monthData->gross_profit ?? 0);
            $expenseDataArr[] = (float)($monthData->total_expenses ?? 0);
        }

        return view('profit-analysis.index', compact(
            'profitData', 'totalSales', 'totalCost', 'grossProfit',
            'totalExpenses', 'netProfit', 'months', 'salesData',
            'costData', 'profitDataArr', 'expenseDataArr', 'year'
        ));
    }

    public function generate()
    {
        try {
            DB::beginTransaction();

            $year = now()->year;
            $month = now()->month;

            // Calculate monthly sales and cost
            $salesData = SalesInvoice::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'completed')
                ->select(
                    DB::raw('SUM(total) as total_sales'),
                    DB::raw('SUM(CASE WHEN id IS NOT NULL THEN 0 ELSE 0 END) as total_cost')
                )
                ->first();

            // Calculate cost from invoice items
            $costData = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->whereYear('sales_invoices.date', $year)
                ->whereMonth('sales_invoices.date', $month)
                ->where('sales_invoices.status', 'completed')
                ->select(DB::raw('SUM(sales_invoice_items.cost_price * sales_invoice_items.quantity) as total_cost'))
                ->first();

            $totalSales = $salesData->total_sales ?? 0;
            $totalCost = $costData->total_cost ?? 0;
            $grossProfit = $totalSales - $totalCost;

            // Monthly expenses
            $totalExpenses = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');

            $netProfit = $grossProfit - $totalExpenses;

            // Update or create profit analysis
            ProfitAnalysis::updateOrCreate(
                ['year' => $year, 'month' => $month],
                [
                    'total_sales' => $totalSales,
                    'total_cost' => $totalCost,
                    'gross_profit' => $grossProfit,
                    'total_expenses' => $totalExpenses,
                    'net_profit' => $netProfit,
                ]
            );

            DB::commit();

            return redirect()->route('profit-analysis.index')
                ->with('success', 'Profit analysis generated for ' . date('F Y'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}