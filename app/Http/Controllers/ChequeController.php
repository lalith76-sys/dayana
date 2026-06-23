<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ChequeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $cheques = Cheque::with('customer', 'supplier')->latest();

            return DataTables::of($cheques)
                ->addColumn('party_name', function ($cheque) {
                    return $cheque->customer->customer_name ?? $cheque->supplier->supplier_name ?? '-';
                })
                ->addColumn('type_badge', function ($cheque) {
                    return $cheque->type === 'received'
                        ? '<span class="badge badge-success">Received</span>'
                        : '<span class="badge badge-warning">Issued</span>';
                })
                ->addColumn('status_badge', function ($cheque) {
                    $badges = [
                        'pending' => 'warning',
                        'deposited' => 'info',
                        'cleared' => 'success',
                        'returned' => 'danger',
                        'cancelled' => 'secondary',
                    ];
                    return '<span class="badge badge-'.$badges[$cheque->status].'">'.ucfirst($cheque->status).'</span>';
                })
                ->addColumn('action', function ($cheque) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<button class="btn btn-sm btn-info" onclick="updateStatus('.$cheque->id.')"><i class="fas fa-exchange-alt"></i></button>';
                    $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$cheque->id.'"><i class="fas fa-trash"></i></button>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->editColumn('amount', function ($cheque) {
                    return 'Rs. '.number_format($cheque->amount, 2);
                })
                ->editColumn('due_date', function ($cheque) {
                    $dueClass = $cheque->due_date->isPast() && $cheque->status === 'pending' ? 'text-danger font-weight-bold' : '';
                    return '<span class="'.$dueClass.'">'.$cheque->due_date->format('Y-m-d').'</span>';
                })
                ->rawColumns(['type_badge', 'status_badge', 'action', 'due_date'])
                ->make(true);
        }

        return view('cheques.index');
    }

    public function create()
    {
        $customers = Customer::active()->pluck('customer_name', 'id');
        $suppliers = Supplier::active()->pluck('supplier_name', 'id');
        return view('cheques.create', compact('customers', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cheque_number' => 'required|string|max:100',
            'bank_name' => 'required|string|max:200',
            'type' => 'required|in:received,issued',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            Cheque::create($request->all() + ['created_by' => auth()->id()]);
            DB::commit();
            return redirect()->route('cheques.index')->with('success', 'Cheque recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function updateStatus(Request $request, Cheque $cheque)
    {
        $request->validate([
            'status' => 'required|in:pending,deposited,cleared,returned,cancelled',
            'return_reason' => 'required_if:status,returned|nullable|string',
            'returned_date' => 'required_if:status,returned|nullable|date',
        ]);

        try {
            DB::beginTransaction();
            $cheque->update($request->only(['status', 'return_reason', 'returned_date']));
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Cheque status updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Cheque $cheque)
    {
        try {
            DB::beginTransaction();
            $cheque->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cheque deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}