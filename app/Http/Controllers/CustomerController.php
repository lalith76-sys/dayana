<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\CustomerPayment;
use App\Http\Requests\CustomerRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $customers = Customer::latest();

            return DataTables::of($customers)
                ->addColumn('action', function ($customer) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="'.route('customers.ledger', $customer->id).'" class="btn btn-sm btn-info"><i class="fas fa-book"></i></a>';
                    $actions .= '<a href="'.route('customers.statement', $customer->id).'" class="btn btn-sm btn-success"><i class="fas fa-file-invoice"></i></a>';
                    if (auth()->user()->can('customers.edit')) {
                        $actions .= '<a href="'.route('customers.edit', $customer->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('customers.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$customer->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->editColumn('current_balance', function ($customer) {
                    $badge = $customer->current_balance > 0 ? 'warning' : 'success';
                    return '<span class="badge badge-'.$badge.'">Rs. '.number_format($customer->current_balance, 2).'</span>';
                })
                ->editColumn('credit_limit', function ($customer) {
                    return 'Rs. '.number_format($customer->credit_limit, 2);
                })
                ->addColumn('status_badge', function ($customer) {
                    return $customer->is_active 
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->rawColumns(['action', 'status_badge', 'current_balance'])
                ->make(true);
        }

        return view('customers.index');
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(CustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            Customer::create($request->validated() + [
                'customer_code' => $this->generateNumber('CUS-', Customer::class, 'id', 4),
                'created_by' => auth()->id(),
            ]);
            DB::commit();
            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        try {
            DB::beginTransaction();
            $customer->update($request->validated() + ['updated_by' => auth()->id()]);
            DB::commit();
            return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();
            $customer->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Customer deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomers()
    {
        return response()->json(Customer::active()->get(['id', 'customer_name', 'customer_code', 'current_balance']));
    }

    public function ledger(Customer $customer)
    {
        $invoices = SalesInvoice::where('customer_id', $customer->id)
            ->with('items')
            ->latest()
            ->get();
        $payments = CustomerPayment::where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('customers.ledger', compact('customer', 'invoices', 'payments'));
    }

    public function statement(Customer $customer)
    {
        $invoices = SalesInvoice::where('customer_id', $customer->id)
            ->where('sales_type', 'credit')
            ->where('balance', '>', 0)
            ->latest()
            ->get();

        return view('customers.statement', compact('customer', 'invoices'));
    }
}