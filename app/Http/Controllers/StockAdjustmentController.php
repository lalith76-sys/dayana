<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockAdjustment;
use App\Helpers\SystemHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with('item', 'creator')
            ->latest()
            ->paginate(15);
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $items = Item::active()->orderBy('item_name')->get();
        return view('stock-adjustments.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:addition,deduction,defective',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
            'date' => 'nullable|date',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        DB::transaction(function () use ($validated, $item) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => SystemHelper::generateCode('ADJ', StockAdjustment::class, 'id', 6),
                'date' => $validated['date'] ?? now(),
                'item_id' => $item->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'cost_price' => $validated['cost_price'] ?? $item->cost_price,
                'reason' => $validated['reason'],
                'created_by' => Auth::id(),
            ]);

            // Update item stock based on adjustment type
            switch ($validated['type']) {
                case 'addition':
                    $item->increment('stock_quantity', $validated['quantity']);
                    break;
                case 'deduction':
                    $item->decrement('stock_quantity', $validated['quantity']);
                    break;
                case 'defective':
                    $item->decrement('stock_quantity', $validated['quantity']);
                    $item->increment('defective_quantity', $validated['quantity']);
                    break;
            }
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock adjustment created successfully.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load('item', 'creator');
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        $items = Item::active()->orderBy('item_name')->get();
        return view('stock-adjustments.edit', compact('stockAdjustment', 'items'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:addition,deduction,defective',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
            'date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $stockAdjustment) {
            // Reverse original adjustment
            $item = Item::findOrFail($stockAdjustment->item_id);
            $this->reverseAdjustment($item, $stockAdjustment);

            // Apply new adjustment
            $newItem = Item::findOrFail($validated['item_id']);
            $this->applyAdjustment($newItem, $validated['type'], $validated['quantity']);

            $stockAdjustment->update([
                'date' => $validated['date'] ?? now(),
                'item_id' => $newItem->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'cost_price' => $validated['cost_price'] ?? $newItem->cost_price,
                'reason' => $validated['reason'],
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock adjustment updated successfully.');
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        DB::transaction(function () use ($stockAdjustment) {
            $item = Item::findOrFail($stockAdjustment->item_id);
            $this->reverseAdjustment($item, $stockAdjustment);
            $stockAdjustment->delete();
        });

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock adjustment deleted successfully.');
    }

    private function reverseAdjustment($item, $adjustment)
    {
        switch ($adjustment->type) {
            case 'addition':
                $item->decrement('stock_quantity', $adjustment->quantity);
                break;
            case 'deduction':
                $item->increment('stock_quantity', $adjustment->quantity);
                break;
            case 'defective':
                $item->increment('stock_quantity', $adjustment->quantity);
                $item->decrement('defective_quantity', $adjustment->quantity);
                break;
        }
    }

    private function applyAdjustment($item, $type, $quantity)
    {
        switch ($type) {
            case 'addition':
                $item->increment('stock_quantity', $quantity);
                break;
            case 'deduction':
                $item->decrement('stock_quantity', $quantity);
                break;
            case 'defective':
                $item->decrement('stock_quantity', $quantity);
                $item->increment('defective_quantity', $quantity);
                break;
        }
    }
}
