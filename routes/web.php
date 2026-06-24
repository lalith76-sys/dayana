<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceivedNoteController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\ProfitAnalysisController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class)->middleware('permission:categories.view|categories.create|categories.edit|categories.delete');
    Route::get('categories-list', [CategoryController::class, 'getCategories'])->name('categories.list');

    // Items
    Route::resource('items', ItemController::class);
    Route::get('items-search', [ItemController::class, 'search'])->name('items.search');
    Route::get('items-barcode/{barcode}', [ItemController::class, 'getByBarcode'])->name('items.barcode');
    Route::get('stock-card/{item}', [ItemController::class, 'stockCard'])->name('items.stock-card');
    Route::get('item-ledger/{item}', [ItemController::class, 'itemLedger'])->name('items.ledger');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers-list', [SupplierController::class, 'getSuppliers'])->name('suppliers.list');
    Route::get('supplier-ledger/{supplier}', [SupplierController::class, 'ledger'])->name('suppliers.ledger');
    Route::get('supplier-purchase-history/{supplier}', [SupplierController::class, 'purchaseHistory'])->name('suppliers.purchase-history');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::get('customers-list', [CustomerController::class, 'getCustomers'])->name('customers.list');
    Route::get('customer-ledger/{customer}', [CustomerController::class, 'ledger'])->name('customers.ledger');
    Route::get('customer-statement/{customer}', [CustomerController::class, 'statement'])->name('customers.statement');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    Route::post('purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');

    // Goods Received Notes
    Route::resource('grn', GoodsReceivedNoteController::class);
    Route::get('grn/{goodsReceivedNote}/print', [GoodsReceivedNoteController::class, 'print'])->name('grn.print');
    Route::get('grn-po-items/{purchaseOrder}', [GoodsReceivedNoteController::class, 'getPoItems'])->name('grn.po-items');

    // Stock
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');

    // Stock Adjustments
    Route::resource('stock-adjustments', StockAdjustmentController::class);

    // POS
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::post('pos/hold', [PosController::class, 'hold'])->name('pos.hold');
    Route::get('pos/resume/{invoice}', [PosController::class, 'resume'])->name('pos.resume');
    Route::get('pos/get-hold-sales', [PosController::class, 'getHoldSales'])->name('pos.get-hold-sales');

    // Sales Invoices
    Route::resource('sales-invoices', SalesInvoiceController::class);
    Route::get('sales-invoices/{salesInvoice}/print', [SalesInvoiceController::class, 'print'])->name('sales-invoices.print');
    Route::get('sales-invoices/{salesInvoice}/email', [SalesInvoiceController::class, 'email'])->name('sales-invoices.email');

    // Customer Payments
    Route::resource('customer-payments', CustomerPaymentController::class);
    Route::get('customer-payments/{customerPayment}/print', [CustomerPaymentController::class, 'print'])->name('customer-payments.print');
    Route::get('customer-invoices/{customer}', [CustomerPaymentController::class, 'getInvoices'])->name('customer-payments.get-invoices');

    // Supplier Payments
    Route::resource('supplier-payments', SupplierPaymentController::class);
    Route::get('supplier-payments/{supplierPayment}/print', [SupplierPaymentController::class, 'print'])->name('supplier-payments.print');
    Route::get('supplier-orders/{supplier}', [SupplierPaymentController::class, 'getOrders'])->name('supplier-payments.get-orders');

    // Cheques
    Route::resource('cheques', ChequeController::class);
    Route::post('cheques/{cheque}/status', [ChequeController::class, 'updateStatus'])->name('cheques.update-status');

    // Sales Returns
    Route::resource('sales-returns', SalesReturnController::class);
    Route::post('sales-returns/{salesReturn}/approve', [SalesReturnController::class, 'approve'])->name('sales-returns.approve');

    // Purchase Returns
    Route::resource('purchase-returns', PurchaseReturnController::class);
    Route::post('purchase-returns/{purchaseReturn}/approve', [PurchaseReturnController::class, 'approve'])->name('purchase-returns.approve');

    // Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-types', ExpenseTypeController::class);

    // Profit Analysis
    Route::get('profit-analysis', [ProfitAnalysisController::class, 'index'])->name('profit-analysis.index');
    Route::get('profit-analysis/generate', [ProfitAnalysisController::class, 'generate'])->name('profit-analysis.generate');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('inventory/export/{format}', [ReportController::class, 'exportInventory'])->name('inventory.export');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('sales/export/{format}', [ReportController::class, 'exportSales'])->name('sales.export');
        Route::get('purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('purchases/export/{format}', [ReportController::class, 'exportPurchases'])->name('purchases.export');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('financial/export/{format}', [ReportController::class, 'exportFinancial'])->name('financial.export');
    });

    // Administration
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [RoleController::class, 'permissions'])->name('permissions.index');
});

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Api\DashboardController::class, 'index']);
        Route::apiResource('categories', App\Http\Controllers\Api\CategoryApiController::class);
        Route::apiResource('items', App\Http\Controllers\Api\ItemApiController::class);
        Route::apiResource('suppliers', App\Http\Controllers\Api\SupplierApiController::class);
        Route::apiResource('customers', App\Http\Controllers\Api\CustomerApiController::class);
        Route::apiResource('purchase-orders', App\Http\Controllers\Api\PurchaseOrderApiController::class);
        Route::apiResource('sales-invoices', App\Http\Controllers\Api\SalesInvoiceApiController::class);
        Route::post('pos/store', [App\Http\Controllers\Api\PosApiController::class, 'store']);
    });
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
