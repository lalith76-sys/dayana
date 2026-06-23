<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Receipt {{ $customerPayment->receipt_number }}</title>
<style>
body{font-family:Arial,sans-serif;font-size:13px;}
.header{text-align:center;margin-bottom:20px;}
table{width:100%;border-collapse:collapse;margin:10px 0;}
th,td{border:1px solid #333;padding:6px;text-align:left;}
th{background:#eee;}
.text-right{text-align:right;}
.footer{text-align:center;margin-top:30px;font-size:11px;color:#666;}
@media print{.no-print{display:none;}}
</style></head>
<body>
<div class="no-print" style="text-align:right;margin-bottom:10px;">
<button onclick="window.print()">Print</button><button onclick="window.close()">Close</button>
</div>
<div class="header"><h2>Dayana Enterprises</h2><p>Payment Receipt</p></div>
<table>
<tr><th>Receipt #</th><td>{{ $customerPayment->receipt_number }}</td><th>Date</th><td>{{ $customerPayment->date->format('Y-m-d') }}</td></tr>
<tr><th>Customer</th><td colspan="3">{{ $customerPayment->customer->customer_name ?? 'N/A' }}</td></tr>
<tr><th>Invoice</th><td colspan="3">{{ $customerPayment->salesInvoice->invoice_number ?? 'N/A' }}</td></tr>
<tr><th>Amount</th><td colspan="3"><strong>Rs. {{ number_format($customerPayment->amount, 2) }}</strong></td></tr>
<tr><th>Payment Method</th><td>{{ $customerPayment->payment_method }}</td><th>Reference</th><td>{{ $customerPayment->reference_number ?? 'N/A' }}</td></tr>
</table>
@if($customerPayment->notes)<p><strong>Notes:</strong> {{ $customerPayment->notes }}</p>@endif
<div class="footer"><p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p></div>
</body>
</html>
