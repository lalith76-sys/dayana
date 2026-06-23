@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Payment: {{ $supplierPayment->payment_number }}</h3>
    </div>
    <form action="{{ route('supplier-payments.update', $supplierPayment) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment #</label>
                        <input type="text" class="form-control" value="{{ $supplierPayment->payment_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $supplierPayment->date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="supplier_id">Supplier</label>
                        <select class="form-control select2" id="supplier_id" name="supplier_id" required style="width:100%;">
                            @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ old('supplier_id', $supplierPayment->supplier_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="purchase_order_id">Purchase Order</label>
                        <select class="form-control select2" id="purchase_order_id" name="purchase_order_id" style="width:100%;">
                            <option value="">-- Optional --</option>
                            @foreach($orders as $id => $po)
                                <option value="{{ $id }}" {{ old('purchase_order_id', $supplierPayment->purchase_order_id) == $id ? 'selected' : '' }}>{{ $po }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rs.</span></div>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $supplierPayment->amount) }}" min="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="Cash" {{ old('payment_method', $supplierPayment->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ old('payment_method', $supplierPayment->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ old('payment_method', $supplierPayment->payment_method) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="Credit Card" {{ old('payment_method', $supplierPayment->payment_method) == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="reference_number">Reference Number</label>
                <input type="text" class="form-control" id="reference_number" name="reference_number" value="{{ old('reference_number', $supplierPayment->reference_number) }}">
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $supplierPayment->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Payment</button>
            <a href="{{ route('supplier-payments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
