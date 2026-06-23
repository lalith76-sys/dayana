<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #4f81bd; color: white; }
        .text-right { text-align: right; }
        h2 { text-align: center; }
        .summary { margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Purchase Report</h2>
    <div class="summary">Period: {{ $startDate }} to {{ $endDate }}</div>
    <table>
        <thead>
            <tr>
                <th>PO #</th>
                <th>Date</th>
                <th>Supplier</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrders as $po)
            <tr>
                <td>{{ $po->po_number }}</td>
                <td>{{ $po->date->format('Y-m-d') }}</td>
                <td>{{ $po->supplier->supplier_name ?? 'N/A' }}</td>
                <td>{{ ucfirst($po->status) }}</td>
                <td class="text-right">{{ number_format($po->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th class="text-right">{{ number_format($purchaseOrders->sum('total_amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
