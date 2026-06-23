<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cheque extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cheque_number',
        'bank_name',
        'type',
        'customer_id',
        'supplier_id',
        'amount',
        'due_date',
        'status',
        'notes',
        'returned_date',
        'return_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'returned_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', 'pending')
                     ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    public function scopeDueToday($query)
    {
        return $query->where('status', 'pending')
                     ->whereDate('due_date', today());
    }
}