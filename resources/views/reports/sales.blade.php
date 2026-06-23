@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Report</h3>
                <div class="card-tools">
                    <a href="{{ route('reports.sales.export', 'pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('reports.sales.export', 'excel') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.sales') }}" class="row mb-3">
                    <div class="col-md-4">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Sales</span>
                                <span class="info-box-number">Rs. {{ number_format($totalSales, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-percent"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Discount</span>
                                <span class="info-box-number">Rs. {{ number_format($totalDiscount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items Count</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Net Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->date->format('Y-m-d') }}</td>
                            <td>{{ $invoice->customer->customer_name ?? 'Walk-in' }}</td>
                            <td class="text-center">{{ $invoice->items->sum('quantity') }}</td>
                            <td class="text-right">Rs. {{ number_format($invoice->total, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($invoice->discount ?? 0, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format(($invoice->total - ($invoice->discount ?? 0)), 2) }}</td>
                            <td>
                                @php
                                    $badges = ['draft' => 'secondary', 'completed' => 'success', 'cancelled' => 'danger'];
                                @endphp
                                <span class="badge badge-{{ $badges[$invoice->status] ?? 'secondary' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No sales found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if(count($invoices) > 0)
                <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th class="text-right" style="width:50%;">Total Sales:</th>
                                <th class="text-right">Rs. {{ number_format($totalSales, 2) }}</th>
                            </tr>
                            <tr>
                                <th class="text-right">Total Discount:</th>
                                <th class="text-right">Rs. {{ number_format($totalDiscount, 2) }}</th>
                            </tr>
                            <tr>
                                <th class="text-right">Net Total:</th>
                                <th class="text-right">Rs. {{ number_format($totalSales - $totalDiscount, 2) }}</th>
                            </tr>
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
<script>
    $(function() {
        $('#dataTable').DataTable({
            ordering: true,
            info: true,
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: [3, 7] }
            ]
        });
    });
</script>
@endpush
