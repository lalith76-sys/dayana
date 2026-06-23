@extends('layouts.app')

@section('title', 'New Supplier Payment')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Record Supplier Payment</h3>
    </div>
    <form action="{{ route('supplier-payments.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment #</label>
                        <input type="text" class="form-control" value="{{ $paymentNumber }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required style="width:100%;">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ old('supplier_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="purchase_order_id">Purchase Order</label>
                        <select class="form-control select2" id="purchase_order_id" name="purchase_order_id" style="width:100%;">
                            <option value="">-- Optional --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rs.</span></div>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" min="0.01" required>
                        </div>
                        @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="">Select</option>
                            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        @error('payment_method') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="reference_number">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" placeholder="Cheque #, Transaction ID">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Record Payment</button>
            <a href="{{ route('supplier-payments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4' });

        // Load orders when supplier changes
        $('#supplier_id').on('change', function() {
            const supplierId = $(this).val();
            const $poSelect = $('#purchase_order_id');
            $poSelect.html('<option value="">Loading...</option>');
            
            if (supplierId) {
                $.get('{{ url("supplier-orders") }}/' + supplierId, function(data) {
                    $poSelect.html('<option value="">-- Optional --</option>');
                    $.each(data, function(id, text) {
                        $poSelect.append('<option value="'+id+'">'+text+'</option>');
                    });
                });
            } else {
                $poSelect.html('<option value="">-- Optional --</option>');
            }
        });
    });
</script>
@endpush
