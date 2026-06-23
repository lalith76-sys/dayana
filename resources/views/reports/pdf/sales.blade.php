<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #4f81bd; color: white; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h2 { text-align: center; }
        .summary { margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Sales Report</h2>
    <div class="summary">Period: {{ $startDate }} to {{ $endDate }}</div>
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-right">Total</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
            <tr>
                <td>{{ $inv->invoice_number }}</td>
                <td>{{ $inv->date->format('Y-m-d') }}</td>
                <td>{{ $inv->customer->customer_name ?? 'Walk-in' }}</td>
                <td class="text-right">{{ number_format($inv->total, 2) }}</td>
                <td class="text-right">{{ number_format($inv->discount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($inv->total - ($inv->discount ?? 0), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th class="text-right">{{ number_format($invoices->sum('total'), 2) }}</th>
                <th class="text-right">{{ number_format($invoices->sum('discount'), 2) }}</th>
                <th class="text-right">{{ number_format($invoices->sum('total') - $invoices->sum('discount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
