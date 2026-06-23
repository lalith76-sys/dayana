@extends('layouts.app')

@section('title', 'GRN Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">GRN: {{ $goodsReceivedNote->grn_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('grn.print', $goodsReceivedNote) }}" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</a>
            <a href="{{ route('grn.edit', $goodsReceivedNote) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('grn.index') }}" class="btn btn-info btn-sm"><i class="fas fa-list"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th style="width:200px;">GRN #</th><td>{{ $goodsReceivedNote->grn_number }}</td></tr>
            <tr><th>Date</th><td>{{ $goodsReceivedNote->date->format('Y-m-d') }}</td></tr>
            <tr><th>PO #</th><td>{{ $goodsReceivedNote->purchaseOrder->po_number ?? 'N/A' }}</td></tr>
            <tr><th>Supplier</th><td>{{ $goodsReceivedNote->supplier->supplier_name ?? 'N/A' }}</td></tr>
            <tr>
                <th>Status</th>
                <td>@php $badges = ['partial' => 'warning', 'complete' => 'success']; @endphp
                    <span class="badge badge-{{ $badges[$goodsReceivedNote->status] }}">{{ ucfirst($goodsReceivedNote->status) }}</span>
                </td>
            </tr>
        </table>

        <h5 class="mt-3">Received Items</h5>
        <table class="table table-bordered">
            <thead><tr><th>#</th><th>Item</th><th class="text-center">Ordered</th><th class="text-center">Received</th><th class="text-right">Cost Price</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @foreach($goodsReceivedNote->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->ordered_quantity }}</td>
                    <td class="text-center">{{ $item->received_quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->cost_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="5" class="text-right">Total:</th><th class="text-right">Rs. {{ number_format($goodsReceivedNote->items->sum('total'), 2) }}</th></tr>
            </tfoot>
        </table>

        @if($goodsReceivedNote->notes)
        <div class="mt-2"><strong>Notes:</strong><p>{{ $goodsReceivedNote->notes }}</p></div>
        @endif
    </div>
</div>
@endsection
