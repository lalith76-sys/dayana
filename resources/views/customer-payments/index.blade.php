@extends('layouts.app')

@section('title', 'Customer Payments')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Customer Payments</h3>
        <div class="card-tools">
            <a href="{{ route('customer-payments.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Payment</a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Receipt #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Invoice</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
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
            ajax: '{{ route('customer-payments.index') }}',
            columns: [
                {data: 'receipt_number', name: 'receipt_number'},
                {data: 'date', name: 'date'},
                {data: 'customer.customer_name', name: 'customer.customer_name'},
                {data: 'sales_invoice.invoice_number', name: 'sales_invoice.invoice_number', defaultContent: '-'},
                {data: 'amount', name: 'amount'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'reference_number', name: 'reference_number', defaultContent: '-'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
