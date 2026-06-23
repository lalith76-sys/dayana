<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_received_note_items', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_item_id']);
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('goods_received_note_items', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_item_id')->nullable(false)->change();
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
        });
    }
};
