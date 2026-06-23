<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $returns = SalesReturn::with('customer', 'item', 'salesInvoice', 'creator')->latest();

            return datatables()->of($returns)
                ->addColumn('action', function ($r) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('sales-returns.show', $r->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    if ($r->status === 'pending') {
                        $actions .= '<form method="POST" action="'.route('sales-returns.approve', $r->id).'" class="d-inline">';
                        $actions .= csrf_field();
                        $actions .= '<button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>';
                        $actions .= '</form>';
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

        return view('sales-returns.index');
    }

    public function create()
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $invoices = SalesInvoice::where('status', 'completed')->pluck('invoice_number', 'id');
        $items = Item::active()->get();
        $returnNumber = 'SR-' . str_pad((SalesReturn::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);
        return view('sales-returns.create', compact('customers', 'invoices', 'items', 'returnNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'item_id' => 'required|exists:items,id',
            'quantity_returned' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($validated['item_id']);
            $total = $validated['quantity_returned'] * $validated['unit_price'];

            $return = SalesReturn::create([
                'return_number' => 'SR-' . str_pad((SalesReturn::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT),
                'date' => $validated['date'],
                'sales_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'customer_id' => $validated['customer_id'],
                'item_id' => $validated['item_id'],
                'quantity_returned' => $validated['quantity_returned'],
                'unit_price' => $validated['unit_price'],
                'total' => $total,
                'reason' => $validated['reason'],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Adjust stock and customer balance on creation
            $item->increment('stock_quantity', $validated['quantity_returned']);
            $item->increment('returned_quantity', $validated['quantity_returned']);
            Customer::find($validated['customer_id'])->decrement('current_balance', $total);

            DB::commit();
            return redirect()->route('sales-returns.index')->with('success', 'Return created. Pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load('customer', 'item', 'salesInvoice', 'creator');
        return view('sales-returns.show', compact('salesReturn'));
    }

    public function approve(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'pending') {
            return redirect()->back()->with('error', 'Return already processed.');
        }

        $salesReturn->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('sales-returns.index')->with('success', 'Return approved.');
    }
}
