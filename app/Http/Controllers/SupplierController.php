<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\SupplierPayment;
use App\Http\Requests\SupplierRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $suppliers = Supplier::latest();

            return DataTables::of($suppliers)
                ->addColumn('action', function ($supplier) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('suppliers.ledger', $supplier->id).'" class="btn btn-sm btn-info" title="Ledger"><i class="fas fa-book"></i></a>';
                    if (auth()->user()->can('suppliers.edit')) {
                        $actions .= '<a href="'.route('suppliers.edit', $supplier->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('suppliers.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$supplier->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status_badge', function ($supplier) {
                    return $supplier->is_active 
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->editColumn('current_balance', function ($supplier) {
                    return 'Rs. '.number_format($supplier->current_balance, 2);
                })
                ->editColumn('credit_limit', function ($supplier) {
                    return 'Rs. '.number_format($supplier->credit_limit, 2);
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('suppliers.index');
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        try {
            DB::beginTransaction();

            Supplier::create($request->validated() + [
                'supplier_code' => $this->generateNumber('SUP-', Supplier::class, 'id', 4),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        try {
            DB::beginTransaction();
            $supplier->update($request->validated() + ['updated_by' => auth()->id()]);
            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            DB::beginTransaction();
            $supplier->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Supplier deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSuppliers()
    {
        return response()->json(Supplier::active()->get(['id', 'supplier_name', 'supplier_code']));
    }

    public function ledger(Supplier $supplier)
    {
        $purchases = PurchaseOrder::where('supplier_id', $supplier->id)
            ->with('items')
            ->latest()
            ->get();
        $payments = SupplierPayment::where('supplier_id', $supplier->id)
            ->latest()
            ->get();

        return view('suppliers.ledger', compact('supplier', 'purchases', 'payments'));
    }

    public function purchaseHistory(Supplier $supplier)
    {
        $purchaseOrders = PurchaseOrder::with('items.item')
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->paginate(20);

        return view('suppliers.purchase-history', compact('supplier', 'purchaseOrders'));
    }
}