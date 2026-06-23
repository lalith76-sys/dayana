@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit PO: {{ $purchaseOrder->po_number }}</h3>
    </div>
    <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>PO #</label>
                        <input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $purchaseOrder->date->format('Y-m-d')) }}" required>
                        @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required style="width:100%;">
                            @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="purchase_type">Purchase Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('purchase_type') is-invalid @enderror" id="purchase_type" name="purchase_type" required>
                            <option value="cash" {{ old('purchase_type', $purchaseOrder->purchase_type) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit" {{ old('purchase_type', $purchaseOrder->purchase_type) == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                        @error('purchase_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" value="{{ old('payment_method', $purchaseOrder->payment_method) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', optional($purchaseOrder->due_date)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            <div class="card card-secondary mt-3">
                <div class="card-header">
                    <h5 class="card-title">Order Items</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="add-item"><i class="fas fa-plus"></i> Add Item</button>
                    </div>
                </div>
                <div class="card-body p-0">
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
                            @foreach($purchaseOrder->items as $i => $poItem)
                            <tr class="item-row">
                                <td>
                                    <select class="form-control form-control-sm item-select" name="items[{{ $i }}][item_id]" required style="width:100%;">
                                        <option value="">Select Item</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}" {{ $poItem->item_id == $item->id ? 'selected' : '' }}>{{ $item->item_code }} - {{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" class="form-control form-control-sm item-qty" name="items[{{ $i }}][quantity]" min="1" value="{{ $poItem->quantity }}" required></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm item-price" name="items[{{ $i }}][cost_price]" min="0" value="{{ $poItem->cost_price }}" required></td>
                                <td><span class="item-subtotal">Rs. {{ number_format($poItem->total, 2) }}</span></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-times"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th><span id="order-total">Rs. {{ number_format($purchaseOrder->total_amount, 2) }}</span></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $purchaseOrder->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update PO</button>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ count($purchaseOrder->items) }};
    $(function() {
        $('.item-select').select2({ theme: 'bootstrap4' });
        $(document).on('change', '.item-select', function() {
            const price = $(this).find(':selected').data('price');
            if (price) $(this).closest('.item-row').find('.item-price').val(price);
            calcRow($(this).closest('.item-row'));
            calcTotal();
        });
        $(document).on('input', '.item-qty, .item-price', function() {
            calcRow($(this).closest('.item-row'));
            calcTotal();
        });
        $('#add-item').on('click', function() {
            const clone = $('.item-row:first').clone();
            clone.find('input').val('').end().find('.item-qty').val(1).end().find('.item-subtotal').text('Rs. 0.00');
            clone.find('select').each(function() {
                $(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + itemIndex + ']')).val('').select2({ theme: 'bootstrap4' });
            });
            clone.find('input').each(function() {
                $(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + itemIndex + ']'));
            });
            $('#items-table tbody').append(clone);
            itemIndex++;
            calcTotal();
        });
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) { $(this).closest('.item-row').remove(); calcTotal(); }
        });
    });
    function calcRow(row) {
        const qty = parseInt(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        row.find('.item-subtotal').text('Rs. ' + (qty * price).toFixed(2));
    }
    function calcTotal() {
        let total = 0;
        $('.item-row').each(function() {
            total += (parseInt($(this).find('.item-qty').val()) || 0) * (parseFloat($(this).find('.item-price').val()) || 0);
        });
        $('#order-total').text('Rs. ' + total.toFixed(2));
    }
</script>
@endpush
