<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'address',
        'contact_person',
        'phone',
        'email',
        'credit_limit',
        'current_balance',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class);
    }

    public function cheques()
    {
        return $this->morphMany(Cheque::class, 'chequeable');
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
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('supplier_code', 'LIKE', "%{$term}%")
              ->orWhere('supplier_name', 'LIKE', "%{$term}%")
              ->orWhere('phone', 'LIKE', "%{$term}%");
        });
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->current_balance;
    }
}