@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Current Stock</h3>
        <div class="card-tools">
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-exchange-alt"></i> Adjust Stock
            </a>
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-info btn-sm">
                <i class="fas fa-history"></i> View Adjustments
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
                    <th>Stock</th>
                    <th>Defective</th>
                    <th>Stock Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('stock.index') }}',
            columns: [
                {data: 'item_code', name: 'item_code'},
                {data: 'item_name', name: 'item_name'},
                {data: 'category.name', name: 'category.name', defaultContent: '-'},
                {data: 'cost_price', name: 'cost_price', render: function(d) { return 'Rs. ' + parseFloat(d).toLocaleString(); }},
                {data: 'selling_price', name: 'selling_price', render: function(d) { return 'Rs. ' + parseFloat(d).toLocaleString(); }},
                {data: 'stock_quantity', name: 'stock_quantity'},
                {data: 'defective_quantity', name: 'defective_quantity'},
                {data: 'stock_value', name: 'id', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'asc']]
        });
    });
</script>
@endpush