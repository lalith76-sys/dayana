<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'phone',
        'address',
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

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function customerPayments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function cheques()
    {
        return $this->hasMany(Cheque::class);
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
            $q->where('customer_code', 'LIKE', "%{$term}%")
              ->orWhere('customer_name', 'LIKE', "%{$term}%")
              ->orWhere('phone', 'LIKE', "%{$term}%");
        });
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->current_balance;
    }
}