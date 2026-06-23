<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $invoices = SalesInvoice::with('customer', 'items')->latest();

            return datatables()->of($invoices)
                ->addColumn('action', function ($inv) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('sales-invoices.show', $inv->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    $actions .= '<a href="'.route('sales-invoices.edit', $inv->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    $actions .= '<a href="'.route('sales-invoices.print', $inv->id).'" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>';
                    if ($inv->status === 'completed') {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$inv->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status_badge', function ($inv) {
                    $badges = ['draft' => 'secondary', 'completed' => 'success', 'cancelled' => 'danger'];
                    return '<span class="badge badge-'.$badges[$inv->status].'">'.ucfirst($inv->status).'</span>';
                })
                ->editColumn('total', function ($inv) {
                    return 'Rs. '.number_format($inv->total, 2);
                })
                ->editColumn('date', function ($inv) {
                    return $inv->date->format('Y-m-d');
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('sales-invoices.index');
    }

    public function create()
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $items = Item::active()->get();
        $lastInvoice = SalesInvoice::withTrashed()->latest('id')->first();
        $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id : 0) + 1, 5, '0', STR_PAD_LEFT);
        return view('sales-invoices.create', compact('customers', 'items', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'sales_type' => 'required|in:cash,credit',
            'payment_method' => 'nullable|string|max:50',
            'due_date' => 'nullable|date|after_or_equal:date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $itemModel = Item::findOrFail($item['item_id']);
                $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
                $subtotal += $lineTotal;

                // Decrease stock
                $itemModel->decrement('stock_quantity', $item['quantity']);

                $itemsData[] = new SalesInvoiceItem([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $lineTotal,
                    'cost_price' => $itemModel->cost_price,
                ]);
            }

            $discount = $validated['discount'] ?? 0;
            $total = $subtotal - $discount;
            $balance = ($validated['sales_type'] === 'credit') ? $total : 0;

            $lastInvoice = SalesInvoice::withTrashed()->latest('id')->first();
            $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id : 0) + 1, 5, '0', STR_PAD_LEFT);

            $invoice = SalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'] ?? null,
                'sales_type' => $validated['sales_type'],
                'payment_method' => $validated['payment_method'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'completed',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid_amount' => ($validated['sales_type'] === 'cash') ? $total : 0,
                'balance' => $balance,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $invoice->items()->saveMany($itemsData);

            // Update customer balance
            if ($validated['customer_id'] && $validated['sales_type'] === 'credit') {
                $customer = Customer::find($validated['customer_id']);
                $customer->increment('current_balance', $total);
            }

            DB::commit();

            return redirect()->route('sales-invoices.index')
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load('customer', 'items.item', 'creator');
        return view('sales-invoices.show', compact('salesInvoice'));
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $items = Item::active()->get();
        $salesInvoice->load('items');
        return view('sales-invoices.edit', compact('salesInvoice', 'customers', 'items'));
    }

    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'sales_type' => 'required|in:cash,credit',
            'payment_method' => 'nullable|string|max:50',
            'due_date' => 'nullable|date|after_or_equal:date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Reverse original stock changes
            foreach ($salesInvoice->items as $oldItem) {
                $itemModel = Item::find($oldItem->item_id);
                if ($itemModel) {
                    $itemModel->increment('stock_quantity', $oldItem->quantity);
                }
            }

            // Reverse customer balance
            if ($salesInvoice->customer_id && $salesInvoice->sales_type === 'credit') {
                Customer::find($salesInvoice->customer_id)?->decrement('current_balance', $salesInvoice->total);
            }

            // Delete old items
            $salesInvoice->items()->delete();

            // Create new items
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $itemModel = Item::findOrFail($item['item_id']);
                $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
                $subtotal += $lineTotal;

                $itemModel->decrement('stock_quantity', $item['quantity']);

                $itemsData[] = new SalesInvoiceItem([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $lineTotal,
                    'cost_price' => $itemModel->cost_price,
                ]);
            }

            $discount = $validated['discount'] ?? 0;
            $total = $subtotal - $discount;
            $balance = ($validated['sales_type'] === 'credit') ? $total : 0;

            $salesInvoice->update([
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'] ?? null,
                'sales_type' => $validated['sales_type'],
                'payment_method' => $validated['payment_method'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid_amount' => ($validated['sales_type'] === 'cash') ? $total : 0,
                'balance' => $balance,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            $salesInvoice->items()->saveMany($itemsData);

            // Update customer balance
            if ($validated['customer_id'] && $validated['sales_type'] === 'credit') {
                Customer::find($validated['customer_id'])?->increment('current_balance', $total);
            }

            DB::commit();

            return redirect()->route('sales-invoices.index')
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        try {
            DB::beginTransaction();

            // Reverse stock
            foreach ($salesInvoice->items as $item) {
                Item::find($item->item_id)?->increment('stock_quantity', $item->quantity);
            }

            // Reverse customer balance
            if ($salesInvoice->customer_id && $salesInvoice->sales_type === 'credit') {
                Customer::find($salesInvoice->customer_id)?->decrement('current_balance', $salesInvoice->total);
            }

            $salesInvoice->update(['status' => 'cancelled']);
            $salesInvoice->delete();

            DB::commit();

            return redirect()->route('sales-invoices.index')
                ->with('success', 'Invoice cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function print(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load('customer', 'items.item', 'creator');
        return view('sales-invoices.print', compact('salesInvoice'));
    }

    public function email(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load('customer', 'items.item');
        // For now just redirect - email functionality can be implemented later
        return redirect()->route('sales-invoices.show', $salesInvoice)
            ->with('info', 'Email functionality coming soon.');
    }
}
