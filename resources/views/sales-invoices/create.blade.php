@extends('layouts.app')

@section('title', 'New Sales Invoice')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">New Sales Invoice</h3>
    </div>
    <form action="{{ route('sales-invoices.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Invoice #</label>
                        <input type="text" class="form-control" value="{{ $invoiceNumber }}" readonly>
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
                        <label for="customer_id">Customer</label>
                        <select class="form-control select2 @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" style="width:100%;">
                            <option value="">Walk-in Customer</option>
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
                        <label for="sales_type">Sales Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('sales_type') is-invalid @enderror" id="sales_type" name="sales_type" required>
                            <option value="cash" {{ old('sales_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit" {{ old('sales_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                        @error('sales_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" value="{{ old('payment_method') }}" placeholder="Cash, Card, etc.">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                    </div>
                </div>
            </div>

            <div class="card card-secondary mt-3">
                <div class="card-header">
                    <h5 class="card-title">Invoice Items</h5>
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
                                    <th style="width:35%">Item</th>
                                    <th style="width:12%">Qty</th>
                                    <th style="width:15%">Unit Price</th>
                                    <th style="width:12%">Disc</th>
                                    <th style="width:15%">Subtotal</th>
                                    <th style="width:11%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select class="form-control form-control-sm item-select" name="items[0][item_id]" required style="width:100%;">
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}" data-code="{{ $item->item_code }}">
                                                    {{ $item->item_code }} - {{ $item->item_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm item-qty" name="items[0][quantity]" min="1" value="1" required></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm item-price" name="items[0][unit_price]" min="0" required></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm item-disc" name="items[0][discount]" min="0" value="0"></td>
                                    <td><span class="item-subtotal">Rs. 0.00</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Discount:</th>
                                    <td colspan="2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend"><span class="input-group-text">Rs.</span></div>
                                            <input type="number" step="0.01" class="form-control form-control-sm" id="invoice-discount" name="discount" value="{{ old('discount', 0) }}" min="0">
                                        </div>
                                    </td>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Grand Total:</th>
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
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create Invoice</button>
            <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = 1;

    $(function() {
        $('.item-select').select2({ theme: 'bootstrap4' });

        $(document).on('change', '.item-select', function() {
            const price = $(this).find(':selected').data('price');
            if (price) $((this).closest('.item-row').find('.item-price').val(price));
            calcRow($(this).closest('.item-row'));
            calcTotal();
        });

        $(document).on('input', '.item-qty, .item-price, .item-disc', function() {
            calcRow($(this).closest('.item-row'));
            calcTotal();
        });

        $('#invoice-discount').on('input', calcTotal);

        $('#add-item').on('click', function() {
            const clone = $('.item-row:first').clone();
            clone.find('input').val('').end()
                .find('.item-qty').val(1).end()
                .find('.item-subtotal').text('Rs. 0.00');
            clone.find('select').each(function() {
                $(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + itemIndex + ']'))
                    .val('').select2({ theme: 'bootstrap4' });
            });
            clone.find('input').each(function() {
                $(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + itemIndex + ']'));
            });
            $('#items-table tbody').append(clone);
            itemIndex++;
            calcTotal();
        });

        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                calcTotal();
            }
        });
    });

    function calcRow(row) {
        const qty = parseInt(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const disc = parseFloat(row.find('.item-disc').val()) || 0;
        const sub = (qty * price) - disc;
        row.find('.item-subtotal').text('Rs. ' + sub.toFixed(2));
    }

    function calcTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const qty = parseInt($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const disc = parseFloat($(this).find('.item-disc').val()) || 0;
            total += (qty * price) - disc;
        });
        const invDisc = parseFloat($('#invoice-discount').val()) || 0;
        $('#order-total').text('Rs. ' + (total - invDisc).toFixed(2));
    }
</script>
@endpush
