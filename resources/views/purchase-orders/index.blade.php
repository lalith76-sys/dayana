@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Purchase Orders</h3>
        <div class="card-tools">
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Purchase Order
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>PO #</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Type</th>
                    <th>Amount</th>
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
    function approvePO(id) {
        Swal.fire({
            title: 'Approve Purchase Order?',
            text: 'This action cannot be undone!',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('{{ url('purchase-orders') }}/' + id + '/approve', function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#dataTable').DataTable().ajax.reload();
                    }
                }).fail(function() { toastr.error('Error approving PO'); });
            }
        });
    }

    $(function() {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('purchase-orders.index') }}',
            columns: [
                {data: 'po_number', name: 'po_number'},
                {data: 'date', name: 'date'},
                {data: 'supplier.supplier_name', name: 'supplier.supplier_name'},
                {data: 'purchase_type', name: 'purchase_type'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'status_badge', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush