@extends('layouts.app')

@section('title', 'Goods Received Notes')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Goods Received Notes</h3>
        <div class="card-tools">
            <a href="{{ route('grn.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New GRN</a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>GRN #</th>
                    <th>Date</th>
                    <th>PO #</th>
                    <th>Supplier</th>
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
            ajax: '{{ route('grn.index') }}',
            columns: [
                {data: 'grn_number', name: 'grn_number'},
                {data: 'date', name: 'date'},
                {data: 'purchase_order.po_number', name: 'purchase_order.po_number', defaultContent: '-'},
                {data: 'supplier.supplier_name', name: 'supplier.supplier_name', defaultContent: '-'},
                {data: 'status_badge', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
