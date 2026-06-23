@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Expense Management</h3>
        <div class="card-tools">
            @can('expenses.create')
                <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Expense
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount</th>
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
            ajax: '{{ route('expenses.index') }}',
            columns: [
                {data: 'expense_date', name: 'expense_date'},
                {data: 'expense_type.name', name: 'expenseType.name'},
                {data: 'description', name: 'description'},
                {data: 'amount', name: 'amount'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush