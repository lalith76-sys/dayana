@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Items</span>
                <span class="info-box-number">{{ $totalItems }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Stock Value</span>
                <span class="info-box-number">Rs. {{ number_format($totalValue, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Low Stock Items</span>
                <span class="info-box-number">{{ $lowStockCount }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Defective Items</span>
                <span class="info-box-number">{{ $items->sum('defective_quantity') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Stock Balance Report</h3>
        <div class="card-tools">
            <a href="{{ route('reports.inventory.export', 'pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('reports.inventory.export', 'excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Stock Qty</th>
                    <th>Defective</th>
                    <th>Stock Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td class="text-right">Rs. {{ number_format($item->cost_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->selling_price, 2) }}</td>
                    <td class="text-center">{{ $item->stock_quantity }}</td>
                    <td class="text-center">{{ $item->defective_quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->stock_value, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row mt-3">
            <div class="col-md-4 offset-md-8">
                <table class="table table-sm table-bordered">
                    <tr><th class="text-right">Total Stock Value:</th><th class="text-right">Rs. {{ number_format($totalValue, 2) }}</th></tr>
                </table>
            </div>
        </div>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#dataTable').DataTable({
            responsive: true,
            order: [[0, 'asc']]
        });
    });
</script>
@endpush