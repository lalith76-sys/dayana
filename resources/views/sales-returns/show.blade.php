@extends('layouts.app')

@section('title', 'Return Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Return: {{ $salesReturn->return_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('sales-returns.index') }}" class="btn btn-info btn-sm"><i class="fas fa-list"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th style="width:200px;">Return #</th><td>{{ $salesReturn->return_number }}</td></tr>
            <tr><th>Date</th><td>{{ $salesReturn->date->format('Y-m-d') }}</td></tr>
            <tr><th>Invoice</th><td>{{ $salesReturn->salesInvoice->invoice_number ?? 'N/A' }}</td></tr>
            <tr><th>Customer</th><td>{{ $salesReturn->customer->customer_name ?? 'N/A' }}</td></tr>
            <tr><th>Item</th><td>{{ $salesReturn->item->item_name ?? 'N/A' }}</td></tr>
            <tr><th>Quantity</th><td>{{ $salesReturn->quantity_returned }}</td></tr>
            <tr><th>Unit Price</th><td>Rs. {{ number_format($salesReturn->unit_price, 2) }}</td></tr>
            <tr><th>Total</th><td><strong>Rs. {{ number_format($salesReturn->total, 2) }}</strong></td></tr>
            <tr><th>Reason</th><td>{{ $salesReturn->reason }}</td></tr>
            <tr>
                <th>Status</th>
                <td>
                    @php $badges = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger']; @endphp
                    <span class="badge badge-{{ $badges[$salesReturn->status] }}">{{ ucfirst($salesReturn->status) }}</span>
                </td>
            </tr>
            <tr><th>Created By</th><td>{{ $salesReturn->creator->name ?? 'N/A' }}</td></tr>
            <tr><th>Created At</th><td>{{ $salesReturn->created_at->format('Y-m-d H:i:s') }}</td></tr>
        </table>
    </div>
</div>
@endsection