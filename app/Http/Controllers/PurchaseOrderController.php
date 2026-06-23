<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Http\Requests\PurchaseOrderRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $purchaseOrders = PurchaseOrder::with('supplier', 'items')->latest();

            return DataTables::of($purchaseOrders)
                ->addColumn('action', function ($po) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('purchase-orders.print', $po->id).'" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>';
                    $actions .= '<a href="'.route('purchase-orders.edit', $po->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    if ($po->status === 'draft') {
                        $actions .= '<button class="btn btn-sm btn-success" onclick="approvePO('.$po->id.')"><i class="fas fa-check"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status_badge', function ($po) {
                    $badges = ['draft' => 'secondary', 'approved' => 'info', 'received' => 'success', 'cancelled' => 'danger'];
                    return '<span class="badge badge-'.$badges[$po->status].'">'.ucfirst($po->status).'</span>';
                })
                ->editColumn('total_amount', function ($po) {
                    return 'Rs. '.number_format($po->total_amount, 2);
                })
                ->editColumn('date', function ($po) {
                    return $po->date->format('Y-m-d');
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('purchase-orders.index');
    }

    public function create()
    {
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        $items = Item::active()->get();
        return view('purchase-orders.create', compact('suppliers', 'items'));
    }

    public function store(PurchaseOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $po = PurchaseOrder::create([
                'po_number' => $this->generateNumber('PO-', PurchaseOrder::class, 'id', 5),
                'date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'purchase_type' => $request->purchase_type,
                'payment_method' => $request->payment_method,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'notes' => $request->notes,
                'total_amount' => 0,
                'created_by' => auth()->id(),
            ]);

            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $lineTotal = $itemData['quantity'] * $itemData['cost_price'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'cost_price' => $itemData['cost_price'],
                    'total' => $lineTotal,
                ]);

                $totalAmount += $lineTotal;
            }

            $po->update(['total_amount' => $totalAmount]);
            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.item', 'creator');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = \App\Models\Supplier::active()->pluck('supplier_name', 'id');
        $items = \App\Models\Item::active()->get();
        $purchaseOrder->load('items');
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'items'));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();

            // Reverse stock for old items
            foreach ($purchaseOrder->items as $oldItem) {
                \App\Models\Item::find($oldItem->item_id)?->decrement('stock_quantity', $oldItem->quantity);
            }
            $purchaseOrder->items()->delete();

            // Update PO
            $purchaseOrder->update($request->validated() + ['updated_by' => auth()->id()]);

            // Process new items
            $total = 0;
            foreach ($request->items as $item) {
                $itemModel = \App\Models\Item::findOrFail($item['item_id']);
                $lineTotal = $item['quantity'] * $item['cost_price'];
                $total += $lineTotal;

                \App\Models\PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'total' => $lineTotal,
                ]);

                if ($purchaseOrder->status === 'approved') {
                    $itemModel->increment('stock_quantity', $item['quantity']);
                }
            }

            $purchaseOrder->update(['total_amount' => $total]);
            DB::commit();
            return redirect()->route('purchase-orders.index')->with('success', 'PO updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();
            $purchaseOrder->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'PO approved.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();
            $purchaseOrder->update(['status' => 'cancelled']);
            DB::commit();
            return redirect()->back()->with('success', 'PO cancelled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.item');
        $pdf = Pdf::loadView('purchase-orders.pdf', compact('purchaseOrder'));
        return $pdf->stream('PO-'.$purchaseOrder->po_number.'.pdf');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();
            // Reverse stock if approved
            if (in_array($purchaseOrder->status, ['approved', 'partial'])) {
                foreach ($purchaseOrder->items as $item) {
                    \App\Models\Item::find($item->item_id)?->decrement('stock_quantity', $item->quantity);
                }
            }
            $purchaseOrder->update(['status' => 'cancelled']);
            $purchaseOrder->delete();
            DB::commit();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'PO deleted.']);
            }
            return redirect()->route('purchase-orders.index')->with('success', 'PO deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}