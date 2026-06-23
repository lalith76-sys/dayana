@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Suppliers</h3>
        <div class="card-tools">
            @can('suppliers.create')
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Supplier
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>Balance</th>
                    <th>Credit Limit</th>
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
            ajax: '{{ route('suppliers.index') }}',
            columns: [
                {data: 'supplier_code', name: 'supplier_code'},
                {data: 'supplier_name', name: 'supplier_name'},
                {data: 'contact_person', name: 'contact_person', defaultContent: '-'},
                {data: 'phone', name: 'phone', defaultContent: '-'},
                {data: 'current_balance', name: 'current_balance'},
                {data: 'credit_limit', name: 'credit_limit'},
                {data: 'status_badge', name: 'is_active', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush