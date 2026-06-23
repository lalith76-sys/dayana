@extends('layouts.app')

@section('title', 'Profit Analysis')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rs. {{ number_format($totalSales, 2) }}</h3>
                <p>Total Sales ({{ $year }})</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>Rs. {{ number_format($totalCost, 2) }}</h3>
                <p>Total Cost ({{ $year }})</p>
            </div>
            <div class="icon"><i class="fas fa-receipt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rs. {{ number_format($grossProfit, 2) }}</h3>
                <p>Gross Profit ({{ $year }})</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>Rs. {{ number_format($netProfit, 2) }}</h3>
                <p>Net Profit ({{ $year }})</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly P&L Analysis - {{ $year }}</h3>
                <div class="card-tools">
                    <form action="{{ route('profit-analysis.generate') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-sync"></i> Generate This Month
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <canvas id="profitChart" style="height: 350px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Expense Breakdown</h3>
            </div>
            <div class="card-body">
                <canvas id="expensePieChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Monthly Profit & Loss Statement</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Sales</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Gross Profit</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right">Net Profit</th>
                    <th class="text-center">Margin %</th>
                </tr>
            </thead>
            <tbody>
                @php $months_list = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; @endphp
                @foreach($profitData as $data)
                <tr>
                    <td>{{ $months_list[$data->month - 1] }} {{ $data->year }}</td>
                    <td class="text-right">Rs. {{ number_format($data->total_sales, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($data->total_cost, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($data->gross_profit, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($data->total_expenses, 2) }}</td>
                    <td class="text-right {{ $data->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                        <strong>Rs. {{ number_format($data->net_profit, 2) }}</strong>
                    </td>
                    <td class="text-center">
                        @if($data->total_sales > 0)
                            {{ number_format(($data->net_profit / $data->total_sales) * 100, 1) }}%
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray">
                <tr>
                    <th>Total</th>
                    <th class="text-right">Rs. {{ number_format($totalSales, 2) }}</th>
                    <th class="text-right">Rs. {{ number_format($totalCost, 2) }}</th>
                    <th class="text-right">Rs. {{ number_format($grossProfit, 2) }}</th>
                    <th class="text-right">Rs. {{ number_format($totalExpenses, 2) }}</th>
                    <th class="text-right {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        <strong>Rs. {{ number_format($netProfit, 2) }}</strong>
                    </th>
                    <th class="text-center">
                        @if($totalSales > 0)
                            {{ number_format(($netProfit / $totalSales) * 100, 1) }}%
                        @else
                            -
                        @endif
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Profit Chart
    new Chart(document.getElementById('profitChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {label: 'Sales', data: {!! json_encode($salesData) !!}, backgroundColor: 'rgba(60,141,188,0.7)'},
                {label: 'Cost', data: {!! json_encode($costData) !!}, backgroundColor: 'rgba(255,99,132,0.7)'},
                {label: 'Gross Profit', data: {!! json_encode($profitDataArr) !!}, backgroundColor: 'rgba(0,166,90,0.7)'},
                {label: 'Net Profit', data: {!! json_encode($expenseDataArr) !!}, backgroundColor: 'rgba(243,156,18,0.7)'}
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{ticks: {callback: function(v) { return 'Rs.' + v.toLocaleString(); }}}]
            }
        }
    });

    // Expense Pie Chart
    new Chart(document.getElementById('expensePieChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Sales', 'Gross Profit', 'Expenses', 'Net Profit'],
            datasets: [{
                data: [{{ $totalSales }}, {{ $grossProfit }}, {{ $totalExpenses }}, {{ $netProfit }}],
                backgroundColor: ['#3c8dbc', '#00a65a', '#f56954', '#f39c12']
            }]
        },
        options: { responsive: true, legend: { position: 'bottom' } }
    });
</script>
@endpush