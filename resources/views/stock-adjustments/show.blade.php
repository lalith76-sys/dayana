@extends('layouts.app')

@section('title', 'Stock Adjustment Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Adjustment: {{ $stockAdjustment->adjustment_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('stock-adjustments.edit', $stockAdjustment) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th style="width:200px;">Adjustment #</th><td>{{ $stockAdjustment->adjustment_number }}</td></tr>
            <tr><th>Date</th><td>{{ $stockAdjustment->date->format('Y-m-d') }}</td></tr>
            <tr><th>Item</th><td>{{ $stockAdjustment->item->item_name ?? 'N/A' }} ({{ $stockAdjustment->item->item_code ?? '' }})</td></tr>
            <tr>
                <th>Type</th>
                <td>
                    @php
                        $badges = ['addition' => 'success', 'deduction' => 'danger', 'defective' => 'warning'];
                        $labels = ['addition' => 'Addition', 'deduction' => 'Reduction', 'defective' => 'Defective'];
                    @endphp
                    <span class="badge badge-{{ $badges[$stockAdjustment->type] ?? 'secondary' }}">
                        {{ $labels[$stockAdjustment->type] ?? $stockAdjustment->type }}
                    </span>
                </td>
            </tr>
            <tr><th>Quantity</th><td>{{ $stockAdjustment->quantity }}</td></tr>
            <tr><th>Cost Price</th><td>Rs. {{ number_format($stockAdjustment->cost_price, 2) }}</td></tr>
            <tr><th>Reason</th><td>{{ $stockAdjustment->reason }}</td></tr>
            <tr><th>Created By</th><td>{{ $stockAdjustment->creator->name ?? 'N/A' }}</td></tr>
            <tr><th>Created At</th><td>{{ $stockAdjustment->created_at->format('Y-m-d H:i:s') }}</td></tr>
        </table>
    </div>
</div>
@endsection
