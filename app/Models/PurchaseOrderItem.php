<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'quantity',
        'cost_price',
        'total',
        'received_quantity',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function goodsReceivedNoteItems()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class);
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }

    public function getIsFullyReceivedAttribute()
    {
        return $this->received_quantity >= $this->quantity;
    }
}