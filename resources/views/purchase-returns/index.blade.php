@extends('layouts.app')

@section('title', 'Purchase Returns')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Purchase Returns</h3>
        <div class="card-tools">
            <a href="{{ route('purchase-returns.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Return</a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Return #</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
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
            ajax: '{{ route('purchase-returns.index') }}',
            columns: [
                {data: 'return_number', name: 'return_number'},
                {data: 'date', name: 'date'},
                {data: 'supplier.supplier_name', name: 'supplier.supplier_name', defaultContent: '-'},
                {data: 'item.item_name', name: 'item.item_name', defaultContent: '-'},
                {data: 'quantity_returned', name: 'quantity_returned'},
                {data: 'total', name: 'total'},
                {data: 'status_badge', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
