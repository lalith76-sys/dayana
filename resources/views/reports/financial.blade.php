@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-hand-holding-usd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Receivables</span>
                <span class="info-box-number">Rs. {{ number_format($receivables, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-credit-card"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Payables</span>
                <span class="info-box-number">Rs. {{ number_format($payables, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Profit ({{ now()->year }})</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Revenue</th>
                            <th class="text-right">Cost</th>
                            <th class="text-right">Expenses</th>
                            <th class="text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profitData as $p)
                        <tr>
                            <td>{{ Carbon\Carbon::create()->month($p->month)->format('F') }}</td>
                            <td class="text-right">Rs. {{ number_format($p->revenue ?? 0, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($p->cost ?? 0, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($p->expenses ?? 0, 2) }}</td>
                            <td class="text-right">
                                <span class="{{ ($p->net_profit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rs. {{ number_format($p->net_profit ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No profit data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Expenses by Type ({{ now()->year }})</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Expense Type</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                        <tr>
                            <td>{{ $exp->expenseType->name ?? 'N/A' }}</td>
                            <td class="text-right">Rs. {{ number_format($exp->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center">No expenses recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Pending Cheques</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Cheque #</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cheques as $cheque)
                        <tr>
                            <td>{{ $cheque->cheque_number }}</td>
                            <td class="text-right">Rs. {{ number_format($cheque->amount, 2) }}</td>
                            <td>{{ $cheque->date->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">No pending cheques.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
