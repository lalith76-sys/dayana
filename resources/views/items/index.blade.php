@extends('layouts.app')

@section('title', 'Items')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Items</h3>
        <div class="card-tools">
            @can('items.create')
                <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Item
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
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
            ajax: '{{ route('items.index') }}',
            columns: [                {data: 'id', name: 'id', visible: false},                {data: 'item_code', name: 'item_code'},
                {data: 'item_name', name: 'item_name'},
                {data: 'category.name', name: 'category.name', defaultContent: '-'},
                {data: 'brand', name: 'brand', defaultContent: '-'},
                {data: 'cost_price', name: 'cost_price'},
                {data: 'selling_price', name: 'selling_price'},
                {data: 'stock_status', name: 'stock_quantity', searchable: false, orderable: false},
                {data: 'status_badge', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush