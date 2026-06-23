<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_received_note_id',
        'purchase_order_item_id',
        'item_id',
        'ordered_quantity',
        'received_quantity',
        'cost_price',
        'total',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}