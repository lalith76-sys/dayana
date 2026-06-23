<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $returns = PurchaseReturn::with('supplier', 'item', 'purchaseOrder', 'creator')->latest();
            return datatables()->of($returns)
                ->addColumn('action', function ($r) {
                    $actions = '<div class="btn-group"><a href="'.route('purchase-returns.show', $r->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    if ($r->status === 'pending') {
                        $actions .= '<form method="POST" action="'.route('purchase-returns.approve', $r->id).'" class="d-inline">'.csrf_field().'<button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->editColumn('total', fn($r) => 'Rs. '.number_format($r->total, 2))
                ->editColumn('date', fn($r) => $r->date->format('Y-m-d'))
                ->addColumn('status_badge', function ($r) {
                    $badges = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                    return '<span class="badge badge-'.$badges[$r->status].'">'.ucfirst($r->status).'</span>';
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }
        return view('purchase-returns.index');
    }

    public function create()
    {
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        $orders = PurchaseOrder::where('status', 'approved')->pluck('po_number', 'id');
        $items = Item::active()->get();
        $returnNumber = 'PR-' . str_pad((PurchaseReturn::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);
        return view('purchase-returns.create', compact('suppliers', 'orders', 'items', 'returnNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'item_id' => 'required|exists:items,id',
            'quantity_returned' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            $item = Item::findOrFail($validated['item_id']);
            $total = $validated['quantity_returned'] * $validated['cost_price'];

            $return = PurchaseReturn::create([
                'return_number' => 'PR-' . str_pad((PurchaseReturn::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT),
                'date' => $validated['date'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'supplier_id' => $validated['supplier_id'],
                'item_id' => $validated['item_id'],
                'quantity_returned' => $validated['quantity_returned'],
                'cost_price' => $validated['cost_price'],
                'total' => $total,
                'reason' => $validated['reason'],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            $item->increment('stock_quantity', $validated['quantity_returned']);
            $item->increment('returned_quantity', $validated['quantity_returned']);
            Supplier::find($validated['supplier_id'])->decrement('current_balance', $total);

            DB::commit();
            return redirect()->route('purchase-returns.index')->with('success', 'Return created. Pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load('supplier', 'item', 'purchaseOrder', 'creator');
        return view('purchase-returns.show', compact('purchaseReturn'));
    }

    public function approve(PurchaseReturn $purchaseReturn)
    {
        if ($purchaseReturn->status !== 'pending') {
            return redirect()->back()->with('error', 'Return already processed.');
        }
        $purchaseReturn->update(['status' => 'approved', 'approved_by' => Auth::id()]);
        return redirect()->route('purchase-returns.index')->with('success', 'Return approved.');
    }
}
