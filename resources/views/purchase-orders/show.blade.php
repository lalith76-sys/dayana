@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">PO: {{ $purchaseOrder->po_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('purchase-orders.print', $purchaseOrder) }}" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</a>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-info btn-sm"><i class="fas fa-list"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th style="width:150px;">PO #</th><td>{{ $purchaseOrder->po_number }}</td></tr>
                    <tr><th>Date</th><td>{{ $purchaseOrder->date->format('Y-m-d') }}</td></tr>
                    <tr><th>Supplier</th><td>{{ $purchaseOrder->supplier->supplier_name ?? 'N/A' }}</td></tr>
                    <tr><th>Type</th><td>{{ ucfirst($purchaseOrder->purchase_type) }}</td></tr>
                    <tr><th>Status</th><td>@php $badges = ['draft' => 'secondary', 'approved' => 'info', 'received' => 'success', 'cancelled' => 'danger']; @endphp
                        <span class="badge badge-{{ $badges[$purchaseOrder->status] ?? 'secondary' }}">{{ ucfirst($purchaseOrder->status) }}</span>
                    </td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th style="width:150px;">Payment Method</th><td>{{ $purchaseOrder->payment_method ?? 'N/A' }}</td></tr>
                    <tr><th>Due Date</th><td>{{ optional($purchaseOrder->due_date)->format('Y-m-d') ?? 'N/A' }}</td></tr>
                    <tr><th>Total Amount</th><td><strong>Rs. {{ number_format($purchaseOrder->total_amount, 2) }}</strong></td></tr>
                    <tr><th>Created By</th><td>{{ $purchaseOrder->creator->name ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>

        <h5 class="mt-3">Order Items</h5>
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-right">Cost Price</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @foreach($purchaseOrder->items as $i => $poItem)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $poItem->item->item_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $poItem->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($poItem->cost_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($poItem->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="4" class="text-right">Total:</th><th class="text-right">Rs. {{ number_format($purchaseOrder->total_amount, 2) }}</th></tr>
            </tfoot>
        </table>

        @if($purchaseOrder->notes)
        <div class="mt-2"><strong>Notes:</strong><p>{{ $purchaseOrder->notes }}</p></div>
        @endif
    </div>
</div>
@endsection
