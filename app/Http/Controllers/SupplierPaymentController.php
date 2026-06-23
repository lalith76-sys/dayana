<?php

namespace App\Http\Controllers;

use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $payments = SupplierPayment::with('supplier', 'purchaseOrder', 'creator')->latest();

            return datatables()->of($payments)
                ->addColumn('action', function ($p) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('supplier-payments.show', $p->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    $actions .= '<a href="'.route('supplier-payments.edit', $p->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    $actions .= '<a href="'.route('supplier-payments.print', $p->id).'" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->editColumn('amount', fn($p) => 'Rs. '.number_format($p->amount, 2))
                ->editColumn('date', fn($p) => $p->date->format('Y-m-d'))
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('supplier-payments.index');
    }

    public function create()
    {
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        $lastPayment = SupplierPayment::withTrashed()->latest('id')->first();
        $paymentNumber = 'SP-' . str_pad(($lastPayment ? $lastPayment->id : 0) + 1, 5, '0', STR_PAD_LEFT);
        return view('supplier-payments.create', compact('suppliers', 'paymentNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $lastPayment = SupplierPayment::withTrashed()->latest('id')->first();
            $paymentNumber = 'SP-' . str_pad(($lastPayment ? $lastPayment->id : 0) + 1, 5, '0', STR_PAD_LEFT);

            $payment = SupplierPayment::create([
                'payment_number' => $paymentNumber,
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Update supplier balance (reduce payables)
            Supplier::find($validated['supplier_id'])->decrement('current_balance', $validated['amount']);

            DB::commit();

            return redirect()->route('supplier-payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load('supplier', 'purchaseOrder', 'creator');
        return view('supplier-payments.show', compact('supplierPayment'));
    }

    public function edit(SupplierPayment $supplierPayment)
    {
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        $orders = PurchaseOrder::where('supplier_id', $supplierPayment->supplier_id)->pluck('po_number', 'id');
        return view('supplier-payments.edit', compact('supplierPayment', 'suppliers', 'orders'));
    }

    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Reverse old payment
            Supplier::find($supplierPayment->supplier_id)->increment('current_balance', $supplierPayment->amount);

            $supplierPayment->update([
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Apply new payment
            Supplier::find($validated['supplier_id'])->decrement('current_balance', $validated['amount']);

            DB::commit();

            return redirect()->route('supplier-payments.index')
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(SupplierPayment $supplierPayment)
    {
        try {
            DB::beginTransaction();
            Supplier::find($supplierPayment->supplier_id)->increment('current_balance', $supplierPayment->amount);
            $supplierPayment->delete();
            DB::commit();
            return redirect()->route('supplier-payments.index')->with('success', 'Payment deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function print(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load('supplier', 'purchaseOrder', 'creator');
        return view('supplier-payments.print', compact('supplierPayment'));
    }

    public function getOrders($supplierId)
    {
        $orders = PurchaseOrder::where('supplier_id', $supplierId)
            ->where('status', 'approved')
            ->pluck('po_number', 'id');
        return response()->json($orders);
    }
}
