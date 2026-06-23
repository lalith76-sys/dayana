@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Details: {{ $user->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width:200px;">ID</th>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $user->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Roles</th>
                <td>
                    @foreach($user->roles as $role)
                        <span class="badge bg-info">{{ $role->name }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Created</th>
                <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
