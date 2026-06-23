@extends('layouts.app')

@section('title', 'Return Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Return: {{ $purchaseReturn->return_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('purchase-returns.index') }}" class="btn btn-info btn-sm"><i class="fas fa-list"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th style="width:200px;">Return #</th><td>{{ $purchaseReturn->return_number }}</td></tr>
            <tr><th>Date</th><td>{{ $purchaseReturn->date->format('Y-m-d') }}</td></tr>
            <tr><th>PO #</th><td>{{ $purchaseReturn->purchaseOrder->po_number ?? 'N/A' }}</td></tr>
            <tr><th>Supplier</th><td>{{ $purchaseReturn->supplier->supplier_name ?? 'N/A' }}</td></tr>
            <tr><th>Item</th><td>{{ $purchaseReturn->item->item_name ?? 'N/A' }}</td></tr>
            <tr><th>Quantity</th><td>{{ $purchaseReturn->quantity_returned }}</td></tr>
            <tr><th>Cost Price</th><td>Rs. {{ number_format($purchaseReturn->cost_price, 2) }}</td></tr>
            <tr><th>Total</th><td><strong>Rs. {{ number_format($purchaseReturn->total, 2) }}</strong></td></tr>
            <tr><th>Reason</th><td>{{ $purchaseReturn->reason }}</td></tr>
            <tr><th>Status</th><td>@php $badges = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger']; @endphp<span class="badge badge-{{ $badges[$purchaseReturn->status] }}">{{ ucfirst($purchaseReturn->status) }}</span></td></tr>
            <tr><th>Created By</th><td>{{ $purchaseReturn->creator->name ?? 'N/A' }}</td></tr>
            <tr><th>Created At</th><td>{{ $purchaseReturn->created_at->format('Y-m-d H:i:s') }}</td></tr>
        </table>
    </div>
</div>
@endsection
