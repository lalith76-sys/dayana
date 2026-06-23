@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Customers</h3>
        <div class="card-tools">
            @can('customers.create')
                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Customer
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
            ajax: '{{ route('customers.index') }}',
            columns: [
                {data: 'customer_code', name: 'customer_code'},
                {data: 'customer_name', name: 'customer_name'},
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