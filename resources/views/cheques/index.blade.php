@extends('layouts.app')

@section('title', 'Cheque Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cheque Register</h3>
        <div class="card-tools">
            @can('cheques.create')
                <a href="{{ route('cheques.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Cheque
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Cheque #</th>
                    <th>Bank</th>
                    <th>Type</th>
                    <th>Party</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Cheque Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="statusForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="chequeId" name="cheque_id">
                    <div class="form-group">
                        <label>Status</label>
                        <select id="updateStatus" name="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="deposited">Deposited</option>
                            <option value="cleared">Cleared</option>
                            <option value="returned">Returned</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" id="returnReasonGroup" style="display: none;">
                        <label>Return Reason</label>
                        <textarea name="return_reason" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group" id="returnDateGroup" style="display: none;">
                        <label>Return Date</label>
                        <input type="date" name="returned_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentChequeId = 0;

    $(function() {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('cheques.index') }}',
            columns: [
                {data: 'cheque_number', name: 'cheque_number'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'type_badge', name: 'type', searchable: false, orderable: false},
                {data: 'party_name', name: 'id', searchable: false},
                {data: 'amount', name: 'amount'},
                {data: 'due_date', name: 'due_date'},
                {data: 'status_badge', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });

        $('#updateStatus').change(function() {
            if ($(this).val() === 'returned') {
                $('#returnReasonGroup, #returnDateGroup').show();
            } else {
                $('#returnReasonGroup, #returnDateGroup').hide();
            }
        });

        $('#statusForm').submit(function(e) {
            e.preventDefault();
            let data = $(this).serialize();
            $.ajax({
                url: '{{ url('cheques') }}/' + currentChequeId + '/status',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#statusModal').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                    }
                },
                error: function() { toastr.error('Error updating status'); }
            });
        });
    });

    function updateStatus(id) {
        currentChequeId = id;
        $('#chequeId').val(id);
        $('#statusModal').modal('show');
    }
</script>
@endpush