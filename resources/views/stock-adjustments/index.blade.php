@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Stock Adjustments</h3>
        <div class="card-tools">
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Adjustment
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
                    <th>#</th>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adjustments as $adj)
                <tr>
                    <td>{{ $adj->adjustment_number }}</td>
                    <td>{{ $adj->date->format('Y-m-d') }}</td>
                    <td>{{ $adj->item->item_name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $badges = ['addition' => 'success', 'deduction' => 'danger', 'defective' => 'warning'];
                            $labels = ['addition' => 'Addition', 'deduction' => 'Reduction', 'defective' => 'Defective'];
                        @endphp
                        <span class="badge badge-{{ $badges[$adj->type] ?? 'secondary' }}">
                            {{ $labels[$adj->type] ?? $adj->type }}
                        </span>
                    </td>
                    <td>{{ $adj->quantity }}</td>
                    <td>{{ Str::limit($adj->reason, 40) }}</td>
                    <td>{{ $adj->creator->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('stock-adjustments.show', $adj) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('stock-adjustments.edit', $adj) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('stock-adjustments.destroy', $adj) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No adjustments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $adjustments->links() }}
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
