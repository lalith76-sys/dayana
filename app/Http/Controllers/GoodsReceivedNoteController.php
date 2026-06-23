<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceivedNoteController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $grns = GoodsReceivedNote::with('supplier', 'purchaseOrder', 'creator')->latest();
            return datatables()->of($grns)
                ->addColumn('action', function ($g) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('grn.show', $g->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    $actions .= '<a href="'.route('grn.edit', $g->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    $actions .= '<a href="'.route('grn.print', $g->id).'" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status_badge', function ($g) {
                    $badges = ['partial' => 'warning', 'complete' => 'success'];
                    return '<span class="badge badge-'.$badges[$g->status].'">'.ucfirst($g->status).'</span>';
                })
                ->editColumn('date', fn($g) => $g->date->format('Y-m-d'))
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }
        return view('grn.index');
    }

    public function create()
    {
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        $orders = PurchaseOrder::with('supplier', 'items.item')
            ->whereIn('status', ['approved', 'partial'])
            ->latest()
            ->get()
            ->mapWithKeys(function ($po) {
                return [$po->id => $po->po_number . ' - ' . ($po->supplier->supplier_name ?? 'N/A')];
            });
        $grnNumber = 'GRN-' . str_pad((GoodsReceivedNote::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);
        return view('grn.create', compact('suppliers', 'orders', 'grnNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.ordered_quantity' => 'required|integer|min:0',
            'items.*.received_quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $grnNumber = 'GRN-' . str_pad((GoodsReceivedNote::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);

            $grn = GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'purchase_order_id' => $validated['purchase_order_id'],
                'supplier_id' => $validated['supplier_id'],
                'date' => $validated['date'],
                'status' => 'complete',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $itemModel = Item::findOrFail($item['item_id']);
                $total = $item['received_quantity'] * $item['cost_price'];

                // Find matching PO item
                $poItem = \App\Models\PurchaseOrderItem::where('purchase_order_id', $validated['purchase_order_id'])
                    ->where('item_id', $item['item_id'])
                    ->first();

                GoodsReceivedNoteItem::create([
                    'goods_received_note_id' => $grn->id,
                    'purchase_order_item_id' => $poItem?->id,
                    'item_id' => $item['item_id'],
                    'ordered_quantity' => $item['ordered_quantity'],
                    'received_quantity' => $item['received_quantity'],
                    'cost_price' => $item['cost_price'],
                    'total' => $total,
                ]);

                // Update stock
                $itemModel->increment('stock_quantity', $item['received_quantity']);
            }

            // Update PO status
            $po = PurchaseOrder::find($validated['purchase_order_id']);
            $po->update(['status' => 'received']);

            DB::commit();
            return redirect()->route('grn.index')->with('success', 'GRN created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show(GoodsReceivedNote $goodsReceivedNote)
    {
        $goodsReceivedNote->load('supplier', 'purchaseOrder', 'items.item', 'creator');
        return view('grn.show', compact('goodsReceivedNote'));
    }

    public function edit(GoodsReceivedNote $goodsReceivedNote)
    {
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        $orders = PurchaseOrder::pluck('po_number', 'id');
        $goodsReceivedNote->load('items');
        return view('grn.edit', compact('goodsReceivedNote', 'suppliers', 'orders'));
    }

    public function update(Request $request, GoodsReceivedNote $goodsReceivedNote)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $goodsReceivedNote->update([
            'date' => $validated['date'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_order_id' => $validated['purchase_order_id'],
            'notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('grn.index')->with('success', 'GRN updated.');
    }

    public function print(GoodsReceivedNote $goodsReceivedNote)
    {
        $goodsReceivedNote->load('supplier', 'purchaseOrder', 'items.item', 'creator');
        return view('grn.print', compact('goodsReceivedNote'));
    }

    public function getPoItems(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.item');
        return response()->json($purchaseOrder->items->map(function ($poItem) {
            return [
                'item_id' => $poItem->item_id,
                'item_name' => $poItem->item->item_name ?? 'N/A',
                'quantity' => $poItem->quantity - ($poItem->received_quantity ?? 0),
                'cost_price' => $poItem->cost_price,
            ];
        }));
    }
}
