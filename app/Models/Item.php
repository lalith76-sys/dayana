<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_code',
        'category_id',
        'item_name',
        'brand',
        'barcode',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'defective_quantity',
        'returned_quantity',
        'reorder_level',
        'status',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function salesInvoiceItems()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('item_code', 'LIKE', "%{$term}%")
              ->orWhere('item_name', 'LIKE', "%{$term}%")
              ->orWhere('barcode', 'LIKE', "%{$term}%");
        });
    }

    public function getStockValueAttribute()
    {
        return $this->stock_quantity * $this->cost_price;
    }
}