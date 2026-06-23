<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $payments = CustomerPayment::with('customer', 'salesInvoice', 'creator')->latest();

            return datatables()->of($payments)
                ->addColumn('action', function ($p) {
                    return '<div class="btn-group">
                        <a href="'.route('customer-payments.show', $p->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="'.route('customer-payments.edit', $p->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="'.route('customer-payments.print', $p->id).'" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>
                    </div>';
                })
                ->editColumn('amount', fn($p) => 'Rs. '.number_format($p->amount, 2))
                ->editColumn('date', fn($p) => $p->date->format('Y-m-d'))
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customer-payments.index');
    }

    public function create()
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $receiptNumber = 'RCPT-' . str_pad((CustomerPayment::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);
        return view('customer-payments.create', compact('customers', 'receiptNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $receiptNumber = 'RCPT-' . str_pad((CustomerPayment::withTrashed()->latest('id')->first()?->id ?? 0) + 1, 5, '0', STR_PAD_LEFT);

            $payment = CustomerPayment::create([
                'receipt_number' => $receiptNumber,
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'],
                'sales_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Reduce customer receivable balance
            Customer::find($validated['customer_id'])->decrement('current_balance', $validated['amount']);

            // Update invoice paid amount if linked
            if (!empty($validated['sales_invoice_id'])) {
                $invoice = SalesInvoice::find($validated['sales_invoice_id']);
                $invoice->increment('paid_amount', $validated['amount']);
                $invoice->decrement('balance', $validated['amount']);
            }

            DB::commit();
            return redirect()->route('customer-payments.index')->with('success', 'Payment recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show(CustomerPayment $customerPayment)
    {
        $customerPayment->load('customer', 'salesInvoice', 'creator');
        return view('customer-payments.show', compact('customerPayment'));
    }

    public function edit(CustomerPayment $customerPayment)
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $invoices = SalesInvoice::where('customer_id', $customerPayment->customer_id)->pluck('invoice_number', 'id');
        return view('customer-payments.edit', compact('customerPayment', 'customers', 'invoices'));
    }

    public function update(Request $request, CustomerPayment $customerPayment)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Reverse old payment
            Customer::find($customerPayment->customer_id)->increment('current_balance', $customerPayment->amount);
            if ($customerPayment->sales_invoice_id) {
                $oldInv = SalesInvoice::find($customerPayment->sales_invoice_id);
                if ($oldInv) {
                    $oldInv->decrement('paid_amount', $customerPayment->amount);
                    $oldInv->increment('balance', $customerPayment->amount);
                }
            }

            $customerPayment->update([
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'],
                'sales_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            // Apply new payment
            Customer::find($validated['customer_id'])->decrement('current_balance', $validated['amount']);
            if (!empty($validated['sales_invoice_id'])) {
                $inv = SalesInvoice::find($validated['sales_invoice_id']);
                $inv->increment('paid_amount', $validated['amount']);
                $inv->decrement('balance', $validated['amount']);
            }

            DB::commit();
            return redirect()->route('customer-payments.index')->with('success', 'Payment updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(CustomerPayment $customerPayment)
    {
        try {
            DB::beginTransaction();
            Customer::find($customerPayment->customer_id)->increment('current_balance', $customerPayment->amount);
            if ($customerPayment->sales_invoice_id) {
                $inv = SalesInvoice::find($customerPayment->sales_invoice_id);
                if ($inv) {
                    $inv->decrement('paid_amount', $customerPayment->amount);
                    $inv->increment('balance', $customerPayment->amount);
                }
            }
            $customerPayment->delete();
            DB::commit();
            return redirect()->route('customer-payments.index')->with('success', 'Payment deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function print(CustomerPayment $customerPayment)
    {
        $customerPayment->load('customer', 'salesInvoice', 'creator');
        return view('customer-payments.print', compact('customerPayment'));
    }

    public function getInvoices($customerId)
    {
        $invoices = SalesInvoice::where('customer_id', $customerId)
            ->where('balance', '>', 0)
            ->pluck('invoice_number', 'id');
        return response()->json($invoices);
    }
}
