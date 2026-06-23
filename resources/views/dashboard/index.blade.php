@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rs. {{ number_format($todaySales, 2) }}</h3>
                <p>Today's Sales</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('sales-invoices.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rs. {{ number_format($monthlySales, 2) }}</h3>
                <p>Monthly Sales</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <a href="{{ route('sales-invoices.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Rs. {{ number_format($outstandingReceivables, 2) }}</h3>
                <p>Receivables</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <a href="{{ route('customers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rs. {{ number_format($outstandingPayables, 2) }}</h3>
                <p>Payables</p>
            </div>
            <div class="icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <a href="{{ route('suppliers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rs. {{ number_format($stockValue, 2) }}</h3>
                <p>Stock Value</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
            <a href="{{ route('stock.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $lowStockCount }}</h3>
                <p>Low Stock Items</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('reports.inventory') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rs. {{ number_format($pendingCheques, 2) }}</h3>
                <p>Pending Cheques</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-check"></i>
            </div>
            <a href="{{ route('cheques.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>Rs. {{ number_format($monthlyProfit, 2) }}</h3>
                <p>Monthly Profit</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <a href="{{ route('profit-analysis.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Sales Trend {{ date('Y') }}</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profit Trend {{ date('Y') }}</h3>
            </div>
            <div class="card-body">
                <canvas id="profitChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Selling Items</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty Sold</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSellingItems as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->total_qty }}</td>
                                <td>Rs. {{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">No data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Expense Analysis</h3>
            </div>
            <div class="card-body">
                <canvas id="expenseChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->customer->customer_name ?? 'Walk-in' }}</td>
                                <td>{{ $invoice->date->format('Y-m-d') }}</td>
                                <td>{{ ucfirst($invoice->sales_type) }}</td>
                                <td>Rs. {{ number_format($invoice->total, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $invoice->status == 'completed' ? 'success' : ($invoice->status == 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sales Chart
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Sales (Rs.)',
                data: {!! json_encode($salesData) !!},
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: 3,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        callback: function(value) { return 'Rs.' + value.toLocaleString(); }
                    }
                }]
            }
        }
    });

    // Profit Chart
    var profitCtx = document.getElementById('profitChart').getContext('2d');
    var profitChart = new Chart(profitCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Net Profit (Rs.)',
                data: {!! json_encode($profitTrend) !!},
                backgroundColor: 'rgba(0, 166, 90, 0.8)',
                borderColor: 'rgba(0, 166, 90, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        callback: function(value) { return 'Rs.' + value.toLocaleString(); }
                    }
                }]
            }
        }
    });

    // Expense Chart
    var expenseCtx = document.getElementById('expenseChart').getContext('2d');
    var expenseData = {!! json_encode($expenseData) !!};
    var expenseLabels = expenseData.map(function(e) { return e.expense_type ? e.expense_type.name : 'Unknown'; });
    var expenseAmounts = expenseData.map(function(e) { return e.total; });
    
    var expenseChart = new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: expenseLabels,
            datasets: [{
                data: expenseAmounts,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        }
    });
</script>
@endpush