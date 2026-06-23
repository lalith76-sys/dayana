@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Invoice: {{ $salesInvoice->invoice_number }}</h3>
    </div>
    <form action="{{ route('sales-invoices.update', $salesInvoice) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Invoice #</label>
                        <input type="text" class="form-control" value="{{ $salesInvoice->invoice_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $salesInvoice->date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select class="form-control select2" id="customer_id" name="customer_id" style="width:100%;">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $id => $name)
                                <option value="{{ $id }}" {{ old('customer_id', $salesInvoice->customer_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sales_type">Sales Type</label>
                        <select class="form-control" id="sales_type" name="sales_type" required>
                            <option value="cash" {{ old('sales_type', $salesInvoice->sales_type) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit" {{ old('sales_type', $salesInvoice->sales_type) == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" value="{{ old('payment_method', $salesInvoice->payment_method) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', optional($salesInvoice->due_date)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Invoice</button>
            <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
