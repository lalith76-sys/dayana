<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ItemRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $items = Item::with('category')->latest();

            return DataTables::of($items)
                ->addColumn('action', function ($item) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('items.stock-card', $item->id).'" class="btn btn-sm btn-info"><i class="fas fa-archive"></i></a>';
                    if (auth()->user()->can('items.edit')) {
                        $actions .= '<a href="'.route('items.edit', $item->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('items.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$item->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status_badge', function ($item) {
                    $badges = [
                        'active' => '<span class="badge badge-success">Active</span>',
                        'inactive' => '<span class="badge badge-warning">Inactive</span>',
                        'discontinued' => '<span class="badge badge-danger">Discontinued</span>',
                    ];
                    return $badges[$item->status] ?? $item->status;
                })
                ->addColumn('stock_status', function ($item) {
                    if ($item->stock_quantity <= 0) {
                        return '<span class="badge badge-danger">Out of Stock</span>';
                    }
                    if ($item->stock_quantity <= $item->reorder_level) {
                        return '<span class="badge badge-warning">Low Stock</span>';
                    }
                    return '<span class="badge badge-success">In Stock</span>';
                })
                ->editColumn('cost_price', function ($item) {
                    return 'Rs. '.number_format($item->cost_price, 2);
                })
                ->editColumn('selling_price', function ($item) {
                    return 'Rs. '.number_format($item->selling_price, 2);
                })
                ->rawColumns(['action', 'status_badge', 'stock_status'])
                ->make(true);
        }

        $categories = Category::active()->pluck('name', 'id');
        return view('items.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::active()->pluck('name', 'id');
        return view('items.create', compact('categories'));
    }

    public function store(ItemRequest $request)
    {
        try {
            DB::beginTransaction();

            Item::create($request->validated() + [
                'item_code' => $this->generateNumber('ITM-', Item::class, 'id', 4),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('items.index')
                ->with('success', 'Item created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Item $item)
    {
        $categories = Category::active()->pluck('name', 'id');
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(ItemRequest $request, Item $item)
    {
        try {
            DB::beginTransaction();

            $item->update($request->validated() + [
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('items.index')
                ->with('success', 'Item updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Item $item)
    {
        try {
            DB::beginTransaction();
            $item->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search()
    {
        $term = request('q');
        $items = Item::active()
            ->where(function ($query) use ($term) {
                $query->where('item_code', 'LIKE', "%{$term}%")
                    ->orWhere('item_name', 'LIKE', "%{$term}%")
                    ->orWhere('barcode', 'LIKE', "%{$term}%");
            })
            ->limit(20)
            ->get(['id', 'item_code', 'item_name', 'selling_price', 'stock_quantity']);

        return response()->json($items);
    }

    public function getByBarcode($barcode)
    {
        $item = Item::where('barcode', $barcode)
            ->orWhere('item_code', $barcode)
            ->first();

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json($item);
    }

    public function stockCard(Item $item)
    {
        $transactions = collect();

        // Purchase receipts
        $purchases = DB::table('goods_received_note_items')
            ->join('goods_received_notes', 'goods_received_note_items.goods_received_note_id', '=', 'goods_received_notes.id')
            ->where('goods_received_note_items.item_id', $item->id)
            ->select(
                'goods_received_notes.date',
                DB::raw("'GRN' as type"),
                'goods_received_note_items.received_quantity as in_qty',
                DB::raw("0 as out_qty"),
                'goods_received_note_items.cost_price as unit_price',
                'goods_received_notes.grn_number as reference'
            )
            ->get();

        // Sales
        $sales = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoice_items.item_id', $item->id)
            ->where('sales_invoices.status', 'completed')
            ->select(
                'sales_invoices.date',
                DB::raw("'SALE' as type"),
                DB::raw("0 as in_qty"),
                'sales_invoice_items.quantity as out_qty',
                'sales_invoice_items.unit_price as unit_price',
                'sales_invoices.invoice_number as reference'
            )
            ->get();

        // Stock Adjustments
        $adjustments = DB::table('stock_adjustments')
            ->where('item_id', $item->id)
            ->select(
                'date',
                DB::raw("'ADJ' as type"),
                DB::raw("CASE WHEN type = 'addition' THEN quantity ELSE 0 END as in_qty"),
                DB::raw("CASE WHEN type = 'deduction' THEN quantity ELSE 0 END as out_qty"),
                'cost_price as unit_price',
                'adjustment_number as reference'
            )
            ->get();

        $transactions = $purchases->concat($sales)->concat($adjustments)
            ->sortBy('date');

        $runningBalance = 0;
        $transactions = $transactions->map(function ($t) use (&$runningBalance) {
            $runningBalance += $t->in_qty - $t->out_qty;
            $t->balance = $runningBalance;
            return $t;
        });

        return view('items.stock-card', compact('item', 'transactions'));
    }

    public function itemLedger(Item $item)
    {
        return $this->stockCard($item);
    }
}