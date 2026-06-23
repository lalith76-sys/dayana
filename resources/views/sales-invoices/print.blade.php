<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $salesInvoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; }
        .summary table { width: 300px; margin-left: auto; }
        .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #666; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:right;margin-bottom:10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>Dayana Enterprises</h2>
        <p>Sales Invoice</p>
    </div>

    <table>
        <tr><td style="width:50%;border:none;"><strong>Invoice #:</strong> {{ $salesInvoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $salesInvoice->date->format('Y-m-d') }}</td>
            <td style="border:none;"><strong>Customer:</strong> {{ $salesInvoice->customer->customer_name ?? 'Walk-in' }}<br>
            <strong>Type:</strong> {{ ucfirst($salesInvoice->sales_type) }}</td></tr>
    </table>

    <table>
        <thead><tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th></tr></thead>
        <tbody>
            @foreach($salesInvoice->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">Rs. {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr><td style="border:none;"><strong>Subtotal:</strong></td><td class="text-right" style="border:none;">Rs. {{ number_format($salesInvoice->subtotal, 2) }}</td></tr>
            <tr><td style="border:none;"><strong>Discount:</strong></td><td class="text-right" style="border:none;">Rs. {{ number_format($salesInvoice->discount ?? 0, 2) }}</td></tr>
            <tr><td style="border:none;"><strong>Total:</strong></td><td class="text-right" style="border:none;"><strong>Rs. {{ number_format($salesInvoice->total, 2) }}</strong></td></tr>
            <tr><td style="border:none;"><strong>Paid:</strong></td><td class="text-right" style="border:none;">Rs. {{ number_format($salesInvoice->paid_amount, 2) }}</td></tr>
            <tr><td style="border:none;"><strong>Balance:</strong></td><td class="text-right" style="border:none;">Rs. {{ number_format($salesInvoice->balance, 2) }}</td></tr>
        </table>
    </div>

    @if($salesInvoice->notes)
    <div style="margin-top:15px;">
        <strong>Notes:</strong> {{ $salesInvoice->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
