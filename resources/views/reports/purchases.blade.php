@extends('layouts.app')

@section('title', 'Purchase Report')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Purchase Report</h3>
                <div class="card-tools">
                    <a href="{{ route('reports.purchases.export', 'pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.purchases') }}" class="row mb-3">
                    <div class="col-md-4">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </form>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Purchases</span>
                                <span class="info-box-number">Rs. {{ number_format($totalPurchases, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>PO #</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                        <tr>
                            <td>{{ $po->po_number }}</td>
                            <td>{{ $po->date->format('Y-m-d') }}</td>
                            <td>{{ $po->supplier->supplier_name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($po->purchase_type) }}</td>
                            <td>
                                @php
                                    $badges = ['draft' => 'secondary', 'approved' => 'info', 'received' => 'success', 'cancelled' => 'danger'];
                                @endphp
                                <span class="badge badge-{{ $badges[$po->status] ?? 'secondary' }}">{{ ucfirst($po->status) }}</span>
                            </td>
                            <td class="text-right">Rs. {{ number_format($po->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No purchases found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if(count($purchaseOrders) > 0)
                <div class="row mt-3">
                    <div class="col-md-4 offset-md-8">
                        <table class="table table-sm table-bordered">
                            <tr><th class="text-right">Total Purchases:</th><th class="text-right">Rs. {{ number_format($totalPurchases, 2) }}</th></tr>
                        </table>
                    </div>
                </div>
                @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>$(function() { $('#dataTable').DataTable({ ordering: true, pageLength: 25 }); });</script>
@endpush
