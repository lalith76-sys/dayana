@extends('layouts.app')

@section('title', 'Supplier Payments')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Supplier Payments</h3>
        <div class="card-tools">
            <a href="{{ route('supplier-payments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Payment
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
                    <th>Payment #</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>PO #</th>
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
            ajax: '{{ route('supplier-payments.index') }}',
            columns: [
                {data: 'payment_number', name: 'payment_number'},
                {data: 'date', name: 'date'},
                {data: 'supplier.supplier_name', name: 'supplier.supplier_name'},
                {data: 'purchase_order.po_number', name: 'purchase_order.po_number', defaultContent: '-'},
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
