<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\ProfitAnalysis;
use App\Http\Requests\ExpenseRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $expenses = Expense::with('expenseType')->latest();

            return DataTables::of($expenses)
                ->addColumn('action', function ($expense) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('expenses.edit', $expense->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$expense->id.'"><i class="fas fa-trash"></i></button>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->editColumn('amount', function ($expense) {
                    return 'Rs. '.number_format($expense->amount, 2);
                })
                ->editColumn('expense_date', function ($expense) {
                    return $expense->expense_date->format('Y-m-d');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('expenses.index');
    }

    public function create()
    {
        $expenseTypes = ExpenseType::active()->pluck('name', 'id');
        return view('expenses.create', compact('expenseTypes'));
    }

    public function store(ExpenseRequest $request)
    {
        try {
            DB::beginTransaction();

            $expense = Expense::create($request->validated() + [
                'created_by' => auth()->id(),
            ]);

            // Update profit analysis
            $profitAnalysis = ProfitAnalysis::firstOrCreate(
                ['year' => now()->year, 'month' => now()->month],
                ['total_sales' => 0, 'total_cost' => 0, 'gross_profit' => 0, 'total_expenses' => 0, 'net_profit' => 0]
            );
            $profitAnalysis->increment('total_expenses', $request->amount);
            $profitAnalysis->decrement('net_profit', $request->amount);

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Expense $expense)
    {
        $expenseTypes = ExpenseType::active()->pluck('name', 'id');
        return view('expenses.edit', compact('expense', 'expenseTypes'));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        try {
            DB::beginTransaction();

            // Reverse old expense
            $profitAnalysis = ProfitAnalysis::where('year', $expense->expense_date->year)
                ->where('month', $expense->expense_date->month)
                ->first();
            if ($profitAnalysis) {
                $profitAnalysis->decrement('total_expenses', $expense->amount);
                $profitAnalysis->increment('net_profit', $expense->amount);
            }

            $expense->update($request->validated() + ['updated_by' => auth()->id()]);

            // Apply new expense
            $profitAnalysis = ProfitAnalysis::firstOrCreate(
                ['year' => now()->year, 'month' => now()->month],
                ['total_sales' => 0, 'total_cost' => 0, 'gross_profit' => 0, 'total_expenses' => 0, 'net_profit' => 0]
            );
            $profitAnalysis->increment('total_expenses', $request->amount);
            $profitAnalysis->decrement('net_profit', $request->amount);

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            DB::beginTransaction();

            $profitAnalysis = ProfitAnalysis::where('year', $expense->expense_date->year)
                ->where('month', $expense->expense_date->month)
                ->first();
            if ($profitAnalysis) {
                $profitAnalysis->decrement('total_expenses', $expense->amount);
                $profitAnalysis->increment('net_profit', $expense->amount);
            }

            $expense->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Expense deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}