@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Categories</h3>
        <div class="card-tools">
            @can('categories.create')
                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Category
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Items Count</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
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
            ajax: '{{ route('categories.index') }}',
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description', defaultContent: '-'},
                {data: 'items_count', name: 'items_count', searchable: false},
                {data: 'status', name: 'is_active', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush