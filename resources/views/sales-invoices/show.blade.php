@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Invoice: {{ $salesInvoice->invoice_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('sales-invoices.print', $salesInvoice) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('sales-invoices.edit', $salesInvoice) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('sales-invoices.index') }}" class="btn btn-info btn-sm">
                <i class="fas fa-list"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th style="width:150px;">Invoice #</th><td>{{ $salesInvoice->invoice_number }}</td></tr>
                    <tr><th>Date</th><td>{{ $salesInvoice->date->format('Y-m-d') }}</td></tr>
                    <tr><th>Customer</th><td>{{ $salesInvoice->customer->customer_name ?? 'Walk-in' }}</td></tr>
                    <tr><th>Sales Type</th><td>{{ ucfirst($salesInvoice->sales_type) }}</td></tr>
                    <tr><th>Payment Method</th><td>{{ $salesInvoice->payment_method ?? 'N/A' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th style="width:150px;">Subtotal</th><td class="text-right">Rs. {{ number_format($salesInvoice->subtotal, 2) }}</td></tr>
                    <tr><th>Discount</th><td class="text-right">Rs. {{ number_format($salesInvoice->discount ?? 0, 2) }}</td></tr>
                    <tr><th>Total</th><td class="text-right"><strong>Rs. {{ number_format($salesInvoice->total, 2) }}</strong></td></tr>
                    <tr><th>Paid</th><td class="text-right">Rs. {{ number_format($salesInvoice->paid_amount, 2) }}</td></tr>
                    <tr><th>Balance</th><td class="text-right"><span class="text-{{ $salesInvoice->balance > 0 ? 'danger' : 'success' }}">Rs. {{ number_format($salesInvoice->balance, 2) }}</span></td></tr>
                </table>
            </div>
        </div>

        <h5 class="mt-3">Invoice Items</h5>
        <table class="table table-bordered">
            <thead>
                <tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-right">Price</th><th class="text-right">Discount</th><th class="text-right">Total</th></tr>
            </thead>
            <tbody>
                @foreach($salesInvoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->discount ?? 0, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="5" class="text-right">Total:</th><th class="text-right">Rs. {{ number_format($salesInvoice->total, 2) }}</th></tr>
            </tfoot>
        </table>

        @if($salesInvoice->notes)
        <div class="mt-2">
            <strong>Notes:</strong>
            <p>{{ $salesInvoice->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
