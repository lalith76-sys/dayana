@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Roles</h3>
        <div class="card-tools">
            @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Role
                </a>
            @endcan
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
                    <th>ID</th>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Users Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        @foreach($role->permissions->take(5) as $permission)
                            <span class="badge bg-info">{{ $permission->name }}</span>
                        @endforeach
                        @if($role->permissions->count() > 5)
                            <span class="badge bg-secondary">+{{ $role->permissions->count() - 5 }} more</span>
                        @endif
                    </td>
                    <td>{{ $role->users_count ?? $role->users->count() }}</td>
                    <td>
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($role->name !== 'Super Admin')
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $roles->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#dataTable').DataTable({
            paging: false,
            ordering: true,
            info: false,
        });
    });
</script>
@endpush
