@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">New Purchase Order</h3>
    </div>
    <form action="{{ route('purchase-orders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="purchase_type">Purchase Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('purchase_type') is-invalid @enderror" id="purchase_type" name="purchase_type" required>
                            <option value="">Select Type</option>
                            <option value="cash" {{ old('purchase_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit" {{ old('purchase_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                        @error('purchase_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <input type="text" class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" value="{{ old('payment_method') }}" placeholder="e.g. Bank Transfer, Cheque">
                        @error('payment_method') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        @error('due_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="card card-secondary mt-3">
                <div class="card-header">
                    <h5 class="card-title">Order Items</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="add-item">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="items-table">
                            <thead>
                                <tr>
                                    <th style="width:40%">Item</th>
                                    <th style="width:15%">Quantity</th>
                                    <th style="width:20%">Cost Price</th>
                                    <th style="width:15%">Subtotal</th>
                                    <th style="width:10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select class="form-control form-control-sm item-select" name="items[0][item_id]" required style="width:100%;">
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}" data-code="{{ $item->item_code }}">
                                                    {{ $item->item_code }} - {{ $item->item_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm item-qty" name="items[0][quantity]" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm item-price" name="items[0][cost_price]" min="0" required>
                                    </td>
                                    <td>
                                        <span class="item-subtotal">Rs. 0.00</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total:</th>
                                    <th><span id="order-total">Rs. 0.00</span></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="notes">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create Purchase Order</button>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = 1;

    $(function() {
        $('.item-select').select2({ theme: 'bootstrap4' });

        // Auto-fill cost price when item is selected
        $(document).on('change', '.item-select', function() {
            const price = $(this).find(':selected').data('price');
            if (price) {
                $(this).closest('.item-row').find('.item-price').val(price);
            }
            calculateRow($(this).closest('.item-row'));
            calculateTotal();
        });

        // Calculate on quantity/price change
        $(document).on('input', '.item-qty, .item-price', function() {
            calculateRow($(this).closest('.item-row'));
            calculateTotal();
        });

        // Add item row
        $('#add-item').on('click', function() {
            const clone = $('.item-row:first').clone();
            clone.find('input, select').each(function() {
                const name = $(this).attr('name').replace(/\[\d+\]/, '[' + itemIndex + ']');
                $(this).attr('name', name).val('');
                if ($(this).hasClass('item-qty')) $(this).val(1);
            });
            clone.find('.item-subtotal').text('Rs. 0.00');
            clone.find('.item-select').select2({ theme: 'bootstrap4' });
            $('#items-table tbody').append(clone);
            itemIndex++;
            calculateTotal();
        });

        // Remove item row
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                calculateTotal();
            }
        });
    });

    function calculateRow(row) {
        const qty = parseInt(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const subtotal = qty * price;
        row.find('.item-subtotal').text('Rs. ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    function calculateTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const qty = parseInt($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            total += qty * price;
        });
        $('#order-total').text('Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }
</script>
@endpush
