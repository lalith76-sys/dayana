@extends('layouts.app')

@section('title', 'POS - Point of Sale')

@section('content')
<div class="row" id="pos-app">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Search</h3>
                <div class="card-tools">
                    <button class="btn btn-info btn-sm" onclick="showScanner()">
                        <i class="fas fa-barcode"></i> Scan Barcode
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="productSearch" class="form-control form-control-lg" 
                           placeholder="Search by name, code or barcode..." autofocus>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <div id="searchResults" class="row" style="max-height: 400px; overflow-y: auto;">
                    @foreach($items as $item)
                    <div class="col-md-4 col-sm-6 mb-2">
                        <button class="btn btn-outline-primary btn-block text-left product-btn" 
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->item_name }}"
                                data-price="{{ $item->selling_price }}"
                                data-stock="{{ $item->stock_quantity }}"
                                style="height: 80px; overflow: hidden;">
                            <small>{{ $item->item_code }}</small><br>
                            <strong>{{ Str::limit($item->item_name, 20) }}</strong><br>
                            <small class="text-muted">Rs. {{ number_format($item->selling_price, 2) }}</small>
                            <small class="float-right badge badge-{{ $item->stock_quantity > $item->reorder_level ? 'success' : 'warning' }}">
                                {{ $item->stock_quantity }} in stock
                            </small>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sale Invoice</h3>
            </div>
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="form-group">
                        <label>Customer</label>
                        <select id="customerSelect" class="form-control select2">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center" width="60">Qty</th>
                            <th class="text-right" width="100">Price</th>
                            <th class="text-right" width="100">Total</th>
                            <th width="40"></th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                    </tbody>
                </table>
                
                <div class="p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Sales Type</label>
                                <select id="salesType" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select id="paymentMethod" class="form-control">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6"><strong>Subtotal:</strong></div>
                        <div class="col-6 text-right" id="subtotal">Rs. 0.00</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Discount:</strong></div>
                        <div class="col-6 text-right">
                            <input type="number" id="globalDiscount" class="form-control form-control-sm text-right" 
                                   value="0" min="0" style="width: 120px; display: inline;">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-right" id="totalAmount">Rs. 0.00</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Paid Amount:</strong></div>
                        <div class="col-6 text-right">
                            <input type="number" id="paidAmount" class="form-control form-control-sm text-right" 
                                   value="0" min="0" style="width: 150px; display: inline;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong class="text-success">Balance:</strong></div>
                        <div class="col-6 text-right" id="balanceDisplay"><strong>Rs. 0.00</strong></div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-4">
                        <button class="btn btn-warning btn-block" onclick="holdSale()">
                            <i class="fas fa-pause"></i> Hold
                        </button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-danger btn-block" onclick="clearCart()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-success btn-block btn-lg" onclick="completeSale()">
                            <i class="fas fa-check"></i> Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hold Sales Modal -->
<div class="modal fade" id="holdSalesModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Held Sales</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holdSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->items->count() }}</td>
                            <td>Rs. {{ number_format($sale->total, 2) }}</td>
                            <td>
                                <a href="{{ route('pos.resume', $sale->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-play"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Scanner Modal -->
<div class="modal fade" id="barcodeModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Scan Barcode</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" id="barcodeInput" class="form-control form-control-lg" 
                       placeholder="Scan or enter barcode..." autofocus>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="lookupBarcode()">Lookup</button>
            </div>
        </div>
    </div>
</div>

<div id="receiptModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sale Completed</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="receiptContent">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="printReceipt()"><i class="fas fa-print"></i> Print</button>
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = [];

// Product search
$('#productSearch').on('keyup', function() {
    let query = $(this).val();
    if (query.length >= 1) {
        $.get('{{ route('items.search') }}', {q: query}, function(data) {
            let html = '';
            data.forEach(function(item) {
                html += `<div class="col-md-4 col-sm-6 mb-2">
                    <button class="btn btn-outline-primary btn-block text-left product-btn" 
                            data-id="${item.id}" data-name="${item.item_name}"
                            data-price="${item.selling_price}" data-stock="${item.stock_quantity}">
                        <small>${item.item_code}</small><br>
                        <strong>${item.item_name.substring(0, 20)}</strong><br>
                        <small>Rs. ${parseFloat(item.selling_price).toLocaleString()}</small>
                    </button>
                </div>`;
            });
            $('#searchResults').html(html || '<div class="col-12 text-center">No items found</div>');
        });
    } else {
        location.reload();
    }
});

// Add to cart
$(document).on('click', '.product-btn', function() {
    let item = {
        id: $(this).data('id'),
        name: $(this).data('name'),
        price: parseFloat($(this).data('price')),
        stock: parseInt($(this).data('stock')),
        quantity: 1,
        discount: 0
    };
    
    let existing = cart.find(c => c.id === item.id);
    if (existing) {
        if (existing.quantity < item.stock) {
            existing.quantity++;
        } else {
            toastr.error('Insufficient stock!');
            return;
        }
    } else {
        cart.push(item);
    }
    
    updateCart();
});

function updateCart() {
    let html = '';
    let subtotal = 0;
    
    cart.forEach(function(item, index) {
        let total = item.price * item.quantity - item.discount;
        subtotal += total;
        html += `<tr>
            <td>${item.name.substring(0, 30)}</td>
            <td class="text-center">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <button class="btn btn-xs btn-secondary" onclick="updateQty(${index}, -1)">-</button>
                    </div>
                    <input type="text" class="form-control text-center" value="${item.quantity}" style="width: 40px;">
                    <div class="input-group-append">
                        <button class="btn btn-xs btn-secondary" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                </div>
            </td>
            <td class="text-right">Rs. ${item.price.toLocaleString()}</td>
            <td class="text-right">Rs. ${total.toLocaleString()}</td>
            <td><button class="btn btn-xs btn-danger" onclick="removeItem(${index})">&times;</button></td>
        </tr>`;
    });
    
    $('#cartItems').html(html || '<tr><td colspan="5" class="text-center text-muted">No items added</td></tr>');
    $('#subtotal').text('Rs. ' + subtotal.toLocaleString());
    calculateTotal();
}

function updateQty(index, change) {
    let newQty = cart[index].quantity + change;
    if (newQty < 1) {
        cart.splice(index, 1);
    } else if (newQty <= cart[index].stock) {
        cart[index].quantity = newQty;
    } else {
        toastr.warning('Insufficient stock!');
    }
    updateCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    updateCart();
}

function calculateTotal() {
    let subtotal = 0;
    cart.forEach(function(item) {
        subtotal += item.price * item.quantity - item.discount;
    });
    let discount = parseFloat($('#globalDiscount').val()) || 0;
    let total = Math.max(0, subtotal - discount);
    let paid = parseFloat($('#paidAmount').val()) || 0;
    let balance = Math.max(0, total - paid);
    
    $('#totalAmount').text('Rs. ' + total.toLocaleString());
    $('#balanceDisplay').html('<strong>Rs. ' + balance.toLocaleString() + '</strong>');
}

$('#globalDiscount, #paidAmount').on('change keyup', calculateTotal);

function completeSale() {
    if (cart.length === 0) {
        toastr.error('Please add items to cart');
        return;
    }
    
    let items = cart.map(function(item) {
        return {
            item_id: item.id,
            quantity: item.quantity,
            unit_price: item.price,
            discount: item.discount || 0
        };
    });
    
    let data = {
        items: items,
        customer_id: $('#customerSelect').val(),
        sales_type: $('#salesType').val(),
        payment_method: $('#paymentMethod').val(),
        paid_amount: parseFloat($('#paidAmount').val()) || 0
    };
    
    $.ajax({
        url: '{{ route('pos.store') }}',
        method: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                let html = `<div class="text-center">
                    <h4>Dayana Enterprises</h4>
                    <p>Invoice: ${response.invoice.invoice_number}<br>
                    Date: ${new Date().toLocaleDateString()}<br>
                    Cashier: {{ auth()->user()->name }}</p>
                    <table class="table table-sm">
                        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                        <tbody>`;
                response.invoice.items.forEach(function(item) {
                    html += `<tr><td>${item.item.item_name}</td><td>${item.quantity}</td>
                        <td>Rs. ${parseFloat(item.unit_price).toLocaleString()}</td>
                        <td>Rs. ${parseFloat(item.total).toLocaleString()}</td></tr>`;
                });
                html += `</tbody></table>
                    <h4>Total: Rs. ${parseFloat(response.invoice.total).toLocaleString()}</h4>
                    <p>Thank you for your business!</p>
                </div>`;
                
                $('#receiptContent').html(html);
                $('#receiptModal').modal('show');
                clearCart();
                toastr.success(response.message);
            }
        },
        error: function(xhr) {
            let msg = 'Error processing sale';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            toastr.error(msg);
        }
    });
}

function holdSale() {
    if (cart.length === 0) {
        toastr.error('Cart is empty');
        return;
    }
    
    let items = cart.map(function(item) {
        return {item_id: item.id, quantity: item.quantity, unit_price: item.price, discount: item.discount || 0};
    });
    
    $.ajax({
        url: '{{ route('pos.hold') }}',
        method: 'POST',
        data: JSON.stringify({items: items, customer_id: $('#customerSelect').val()}),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                toastr.success('Sale held successfully');
                clearCart();
            }
        },
        error: function() {
            toastr.error('Error holding sale');
        }
    });
}

function clearCart() {
    cart = [];
    updateCart();
    $('#productSearch').val('').focus();
}

function showScanner() {
    $('#barcodeModal').modal('show');
    setTimeout(function() { $('#barcodeInput').focus(); }, 500);
}

function lookupBarcode() {
    let barcode = $('#barcodeInput').val();
    if (!barcode) return;
    
    $.get('{{ url('items-barcode') }}/' + barcode, function(item) {
        let existing = cart.find(c => c.id === item.id);
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({id: item.id, name: item.item_name, price: parseFloat(item.selling_price), 
                       stock: parseInt(item.stock_quantity), quantity: 1, discount: 0});
        }
        updateCart();
        $('#barcodeModal').modal('hide');
        $('#barcodeInput').val('');
        toastr.success('Item added: ' + item.item_name);
    }).fail(function() {
        toastr.error('Item not found');
    });
}

function printReceipt() {
    var content = $('#receiptContent').html();
    var win = window.open('', '', 'width=300,height=400');
    win.document.write('<html><head><title>Receipt</title>');
    win.document.write('<link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">');
    win.document.write('</head><body>');
    win.document.write(content);
    win.document.write('<script>window.print();window.close();<\/script>');
    win.document.write('</body></html>');
    win.document.close();
}

// Keyboard shortcut for search
$(document).keydown(function(e) {
    if (e.ctrlKey && e.keyCode === 191) { // Ctrl + /
        e.preventDefault();
        $('#productSearch').focus();
    }
    if (e.keyCode === 27) { // ESC to clear
        clearCart();
    }
    if (e.keyCode === 13 && $('#barcodeModal').is(':visible')) {
        lookupBarcode();
    }
});

// Initialize
$('#productSearch').focus();
</script>
@endpush