@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Role: {{ $role->name }}</h3>
    </div>
    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label>Permissions</label>
                <div class="row">
                    @foreach($permissions as $group => $groupPermissions)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">{{ ucfirst($group) }}</h6>
                                <div class="card-tools">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input select-all" id="select-all-{{ $group }}">
                                        <label class="custom-control-label" for="select-all-{{ $group }}">All</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                @foreach($groupPermissions as $permission)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" id="perm-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}"
                                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="perm-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Role</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('.select-all').on('change', function() {
            $(this).closest('.card').find('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });
    });
</script>
@endpush
