@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Role Details: {{ $role->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width:200px;">ID</th>
                <td>{{ $role->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $role->name }}</td>
            </tr>
            <tr>
                <th>Permissions</th>
                <td>
                    @foreach($role->permissions as $permission)
                        <span class="badge bg-info">{{ $permission->name }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>Created</th>
                <td>{{ $role->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td>{{ $role->updated_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
