<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #4f81bd; color: white; }
        .text-right { text-align: right; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Financial Report - {{ now()->year }}</h2>
    
    <h3>Monthly Profit</h3>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Cost</th>
                <th class="text-right">Expenses</th>
                <th class="text-right">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profitData as $p)
            <tr>
                <td>{{ Carbon\Carbon::create()->month($p->month)->format('F') }}</td>
                <td class="text-right">{{ number_format($p->revenue ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($p->cost ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($p->expenses ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($p->net_profit ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Expenses by Type</h3>
    <table>
        <thead>
            <tr><th>Type</th><th class="text-right">Total</th></tr>
        </thead>
        <tbody>
            @foreach($expenses as $exp)
            <tr>
                <td>{{ $exp->expenseType->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($exp->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
