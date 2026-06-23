@extends('layouts.app')

@section('title', 'New GRN')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Create Goods Received Note</h3></div>
    <form action="{{ route('grn.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group"><label>GRN #</label><input type="text" class="form-control" value="{{ $grnNumber }}" readonly></div>
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
            <div class="form-group">
                <label for="purchase_order_id">Purchase Order <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('purchase_order_id') is-invalid @enderror" id="purchase_order_id" name="purchase_order_id" required style="width:100%;">
                    <option value="">Select PO</option>
                    @foreach($orders as $id => $label)
                        <option value="{{ $id }}" {{ old('purchase_order_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('purchase_order_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="card card-secondary mt-3">
                <div class="card-header"><h5 class="card-title">Received Items</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="items-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="width:100px;">Ordered</th>
                                    <th style="width:100px;">Received <span class="text-danger">*</span></th>
                                    <th style="width:120px;">Cost Price <span class="text-danger">*</span></th>
                                    <th style="width:100px;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                <tr id="no-items"><td colspan="5" class="text-center">Select a Purchase Order to load items.</td></tr>
                            </tbody>
                            <tfoot>
                                <tr><th colspan="4" class="text-right">Total:</th><th id="grn-total">Rs. 0.00</th></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save GRN</button>
            <a href="{{ route('grn.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4' });

        $('#purchase_order_id').on('change', function() {
            var poId = $(this).val();
            var $tbody = $('#items-body');
            $tbody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

            if (poId) {
                $.get('{{ url('grn-po-items') }}/' + poId, function(data) {
                    $tbody.empty();
                    if (data.length === 0) {
                        $tbody.html('<tr><td colspan="5" class="text-center">No items found in this PO.</td></tr>');
                        return;
                    }
                    $.each(data, function(i, item) {
                        var row = '<tr>' +
                            '<td><input type="hidden" name="items['+i+'][item_id]" value="'+item.item_id+'">' + item.item_name + '</td>' +
                            '<td class="text-center"><input type="hidden" name="items['+i+'][ordered_quantity]" value="'+item.quantity+'">' + item.quantity + '</td>' +
                            '<td><input type="number" class="form-control form-control-sm item-qty" name="items['+i+'][received_quantity]" value="'+item.quantity+'" min="1" required></td>' +
                            '<td><input type="number" step="0.01" class="form-control form-control-sm item-price" name="items['+i+'][cost_price]" value="'+item.cost_price+'" min="0" required></td>' +
                            '<td class="text-right item-total">Rs. ' + (item.quantity * item.cost_price).toFixed(2) + '</td>' +
                            '</tr>';
                        $tbody.append(row);
                    });
                    calcGRNTotal();
                });
            } else {
                $tbody.html('<tr><td colspan="5" class="text-center">Select a Purchase Order to load items.</td></tr>');
            }
        });

        $(document).on('input', '.item-qty, .item-price', function() {
            var row = $(this).closest('tr');
            var qty = parseInt(row.find('.item-qty').val()) || 0;
            var price = parseFloat(row.find('.item-price').val()) || 0;
            row.find('.item-total').text('Rs. ' + (qty * price).toFixed(2));
            calcGRNTotal();
        });
    });

    function calcGRNTotal() {
        var total = 0;
        $('.item-qty').each(function() {
            var row = $(this).closest('tr');
            var qty = parseInt(row.find('.item-qty').val()) || 0;
            var price = parseFloat(row.find('.item-price').val()) || 0;
            total += qty * price;
        });
        $('#grn-total').text('Rs. ' + total.toFixed(2));
    }
</script>
@endpush
@endsection
