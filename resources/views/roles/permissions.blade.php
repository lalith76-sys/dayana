@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">System Permissions</h3>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($permissions as $group => $groupPermissions)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">{{ ucfirst($group) }}</h6>
                    </div>
                    <div class="card-body py-2">
                        @foreach($groupPermissions as $permission)
                            <span class="badge bg-info d-inline-block mb-1">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
