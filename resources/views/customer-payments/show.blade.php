@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Payment: {{ $customerPayment->receipt_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('customer-payments.print', $customerPayment) }}" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</a>
            <a href="{{ route('customer-payments.edit', $customerPayment) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('customer-payments.index') }}" class="btn btn-info btn-sm"><i class="fas fa-list"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th style="width:200px;">Receipt #</th><td>{{ $customerPayment->receipt_number }}</td></tr>
            <tr><th>Date</th><td>{{ $customerPayment->date->format('Y-m-d') }}</td></tr>
            <tr><th>Customer</th><td>{{ $customerPayment->customer->customer_name ?? 'N/A' }}</td></tr>
            <tr><th>Invoice</th><td>{{ $customerPayment->salesInvoice->invoice_number ?? 'N/A' }}</td></tr>
            <tr><th>Amount</th><td><strong>Rs. {{ number_format($customerPayment->amount, 2) }}</strong></td></tr>
            <tr><th>Payment Method</th><td>{{ $customerPayment->payment_method }}</td></tr>
            <tr><th>Reference #</th><td>{{ $customerPayment->reference_number ?? 'N/A' }}</td></tr>
            <tr><th>Notes</th><td>{{ $customerPayment->notes ?? 'N/A' }}</td></tr>
            <tr><th>Created By</th><td>{{ $customerPayment->creator->name ?? 'N/A' }}</td></tr>
            <tr><th>Created At</th><td>{{ $customerPayment->created_at->format('Y-m-d H:i:s') }}</td></tr>
        </table>
    </div>
</div>
@endsection
