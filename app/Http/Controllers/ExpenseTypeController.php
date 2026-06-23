<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        $expenseTypes = ExpenseType::withCount('expenses')->latest()->paginate(20);
        return view('expense-types.index', compact('expenseTypes'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:expense_types,name']);
        ExpenseType::create($request->all());
        return redirect()->route('expense-types.index')->with('success', 'Expense type created.');
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate(['name' => 'required|string|max:100|unique:expense_types,name,'.$expenseType->id]);
        $expenseType->update($request->all());
        return redirect()->route('expense-types.index')->with('success', 'Expense type updated.');
    }

    public function destroy(ExpenseType $expenseType)
    {
        if ($expenseType->expenses()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete type with associated expenses.');
        }
        $expenseType->delete();
        return redirect()->route('expense-types.index')->with('success', 'Expense type deleted.');
    }
}