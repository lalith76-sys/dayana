<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\ProfitAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $items = Item::active()->where('stock_quantity', '>', 0)->get();
        $holdSales = SalesInvoice::where('is_hold', true)
            ->where('created_by', auth()->id())
            ->get();

        return view('pos.index', compact('customers', 'items', 'holdSales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'sales_type' => 'required|in:cash,credit',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $invoiceNumber = $this->generateNumber('INV-', SalesInvoice::class, 'id', 6);
            $subtotal = 0;
            $totalDiscount = 0;
            $totalCost = 0;

            // Create invoice
            $invoice = SalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'date' => now(),
                'customer_id' => $request->customer_id,
                'sales_type' => $request->sales_type,
                'payment_method' => $request->payment_method,
                'due_date' => $request->sales_type === 'credit' ? now()->addDays(30) : null,
                'status' => 'completed',
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'paid_amount' => $request->paid_amount ?? 0,
                'balance' => 0,
                'created_by' => auth()->id(),
            ]);

            // Process items
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                // Check stock
                if ($item->stock_quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for {$item->item_name}. Available: {$item->stock_quantity}");
                }

                $lineTotal = $itemData['unit_price'] * $itemData['quantity'];
                $lineDiscount = $itemData['discount'] ?? 0;
                $lineFinalTotal = $lineTotal - $lineDiscount;

                // Create invoice item
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $lineDiscount,
                    'total' => $lineFinalTotal,
                    'cost_price' => $item->cost_price,
                ]);

                // Update stock
                $item->decrement('stock_quantity', $itemData['quantity']);

                $subtotal += $lineTotal;
                $totalDiscount += $lineDiscount;
                $totalCost += $item->cost_price * $itemData['quantity'];
            }

            // Calculate totals
            $total = $subtotal - $totalDiscount;
            $balance = $total - ($request->paid_amount ?? 0);

            $invoice->update([
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'total' => $total,
                'balance' => max(0, $balance),
            ]);

            // Update customer balance
            if ($request->customer_id && $request->sales_type === 'credit') {
                $customer = Customer::find($request->customer_id);
                $customer->increment('current_balance', $balance);
            }

            // Update profit analysis
            $this->updateProfitAnalysis($total, $totalCost);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully.',
                    'invoice' => $invoice->load('items.item', 'customer'),
                    'print_url' => route('sales-invoices.print', $invoice->id),
                ]);
            }

            return redirect()->route('sales-invoices.show', $invoice->id)
                ->with('success', 'Sale completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function hold(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $invoiceNumber = $this->generateNumber('HLD-', SalesInvoice::class, 'id', 6);
            $subtotal = 0;
            $totalDiscount = 0;

            $invoice = SalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'date' => now(),
                'customer_id' => $request->customer_id,
                'status' => 'on_hold',
                'is_hold' => true,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $lineTotal = $itemData['unit_price'] * $itemData['quantity'];
                $lineDiscount = $itemData['discount'] ?? 0;

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $lineDiscount,
                    'total' => $lineTotal - $lineDiscount,
                    'cost_price' => $item->cost_price,
                ]);

                $subtotal += $lineTotal;
                $totalDiscount += $lineDiscount;
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'total' => $subtotal - $totalDiscount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale held successfully.',
                'invoice' => $invoice->load('items.item'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function resume(SalesInvoice $invoice)
    {
        if (!$invoice->is_hold) {
            return redirect()->route('pos.index')->with('error', 'Invoice is not on hold.');
        }

        $invoice->load('items.item');
        $customers = Customer::active()->pluck('customer_name', 'id');
        $items = Item::active()->where('stock_quantity', '>', 0)->get();

        return view('pos.resume', compact('invoice', 'customers', 'items'));
    }

    public function getHoldSales()
    {
        $sales = SalesInvoice::with('items.item', 'customer')
            ->where('is_hold', true)
            ->where('created_by', auth()->id())
            ->latest()
            ->get();

        return response()->json($sales);
    }

    private function updateProfitAnalysis($sales, $cost)
    {
        $year = now()->year;
        $month = now()->month;

        $profitAnalysis = ProfitAnalysis::firstOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'total_sales' => 0,
                'total_cost' => 0,
                'gross_profit' => 0,
                'total_expenses' => 0,
                'net_profit' => 0,
            ]
        );

        $profitAnalysis->increment('total_sales', $sales);
        $profitAnalysis->increment('total_cost', $cost);
        $profitAnalysis->increment('gross_profit', $sales - $cost);
        $profitAnalysis->increment('net_profit', $sales - $cost);
    }
}