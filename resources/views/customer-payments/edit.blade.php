@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Edit Payment: {{ $customerPayment->receipt_number }}</h3></div>
    <form action="{{ route('customer-payments.update', $customerPayment) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group"><label>Receipt #</label><input type="text" class="form-control" value="{{ $customerPayment->receipt_number }}" readonly></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label for="date">Date</label><input type="date" class="form-control" id="date" name="date" value="{{ old('date', $customerPayment->date->format('Y-m-d')) }}" required></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select class="form-control select2" id="customer_id" name="customer_id" required style="width:100%;">
                            @foreach($customers as $id => $name)
                                <option value="{{ $id }}" {{ old('customer_id', $customerPayment->customer_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sales_invoice_id">Invoice</label>
                        <select class="form-control select2" id="sales_invoice_id" name="sales_invoice_id" style="width:100%;">
                            <option value="">-- Optional --</option>
                            @foreach($invoices as $id => $inv)
                                <option value="{{ $id }}" {{ old('sales_invoice_id', $customerPayment->sales_invoice_id) == $id ? 'selected' : '' }}>{{ $inv }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rs.</span></div>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $customerPayment->amount) }}" min="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="Cash" {{ old('payment_method', $customerPayment->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ old('payment_method', $customerPayment->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ old('payment_method', $customerPayment->payment_method) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="Credit Card" {{ old('payment_method', $customerPayment->payment_method) == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group"><label for="reference_number">Reference Number</label><input type="text" class="form-control" id="reference_number" name="reference_number" value="{{ old('reference_number', $customerPayment->reference_number) }}"></div>
            <div class="form-group"><label for="notes">Notes</label><textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $customerPayment->notes) }}</textarea></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Payment</button>
            <a href="{{ route('customer-payments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
