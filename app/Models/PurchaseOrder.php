<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'date',
        'supplier_id',
        'purchase_type',
        'payment_method',
        'due_date',
        'status',
        'total_amount',
        'notes',
        'invoice_attachment',
        'created_by',
        'approved_by',
        'updated_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class);
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getReceivedPercentageAttribute()
    {
        $totalItems = $this->items->count();
        if ($totalItems === 0) return 0;
        
        $receivedItems = $this->items->filter(function($item) {
            return $item->received_quantity > 0;
        })->count();

        return ($receivedItems / $totalItems) * 100;
    }
}