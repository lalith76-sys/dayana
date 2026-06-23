@extends('layouts.app')

@section('title', 'Sales Invoices')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sales Invoices</h3>
        <div class="card-tools">
            <a href="{{ route('sales-invoices.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Invoice
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
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
            ajax: '{{ route('sales-invoices.index') }}',
            columns: [
                {data: 'invoice_number', name: 'invoice_number'},
                {data: 'date', name: 'date'},
                {data: 'customer.customer_name', name: 'customer.customer_name', defaultContent: 'Walk-in'},
                {data: 'sales_type', name: 'sales_type'},
                {data: 'total', name: 'total'},
                {data: 'paid_amount', name: 'paid_amount'},
                {data: 'balance', name: 'balance'},
                {data: 'status_badge', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
