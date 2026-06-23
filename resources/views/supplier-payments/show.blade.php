@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Payment: {{ $supplierPayment->payment_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('supplier-payments.print', $supplierPayment) }}" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</a>
            <a href="{{ route('supplier-payments.edit', $supplierPayment) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('supplier-payments.index') }}" class="btn btn-info btn-sm"><i class="fas fa-list"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th style="width:200px;">Payment #</th><td>{{ $supplierPayment->payment_number }}</td></tr>
            <tr><th>Date</th><td>{{ $supplierPayment->date->format('Y-m-d') }}</td></tr>
            <tr><th>Supplier</th><td>{{ $supplierPayment->supplier->supplier_name ?? 'N/A' }}</td></tr>
            <tr><th>Purchase Order</th><td>{{ $supplierPayment->purchaseOrder->po_number ?? 'N/A' }}</td></tr>
            <tr><th>Amount</th><td><strong>Rs. {{ number_format($supplierPayment->amount, 2) }}</strong></td></tr>
            <tr><th>Payment Method</th><td>{{ $supplierPayment->payment_method }}</td></tr>
            <tr><th>Reference #</th><td>{{ $supplierPayment->reference_number ?? 'N/A' }}</td></tr>
            <tr><th>Notes</th><td>{{ $supplierPayment->notes ?? 'N/A' }}</td></tr>
            <tr><th>Created By</th><td>{{ $supplierPayment->creator->name ?? 'N/A' }}</td></tr>
            <tr><th>Created At</th><td>{{ $supplierPayment->created_at->format('Y-m-d H:i:s') }}</td></tr>
        </table>
    </div>
</div>
@endsection
