@extends('layouts.app')

@section('title', 'New Sales Return')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Record Sales Return</h3></div>
    <form action="{{ route('sales-returns.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group"><label>Return #</label><input type="text" class="form-control" value="{{ $returnNumber }}" readonly></div>
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
                        <label for="customer_id">Customer <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required style="width:100%;">
                            <option value="">Select Customer</option>
                            @foreach($customers as $id => $name)
                                <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sales_invoice_id">Original Invoice</label>
                        <select class="form-control select2" id="sales_invoice_id" name="sales_invoice_id" style="width:100%;">
                            <option value="">-- Optional --</option>
                            @foreach($invoices as $id => $inv)
                                <option value="{{ $id }}" {{ old('sales_invoice_id') == $id ? 'selected' : '' }}>{{ $inv }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="item_id">Item <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required style="width:100%;">
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_code }} - {{ $item->item_name }}</option>
                            @endforeach
                        </select>
                        @error('item_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="quantity_returned">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity_returned') is-invalid @enderror" id="quantity_returned" name="quantity_returned" value="{{ old('quantity_returned', 1) }}" min="1" required>
                        @error('quantity_returned') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit_price">Unit Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rs.</span></div>
                            <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" name="unit_price" value="{{ old('unit_price') }}" min="0" required>
                        </div>
                        @error('unit_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="reason">Reason <span class="text-danger">*</span></label>
                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                @error('reason') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit Return</button>
            <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
        $('#item_id').on('change', function() {
            var price = $(this).find(':selected').data('price');
            if (price) $('#unit_price').val(price);
        });
    });
</script>
@endpush
@endsection