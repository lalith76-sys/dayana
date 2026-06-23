<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('item_name', 200);
            $table->string('brand', 100)->nullable();
            $table->string('barcode', 100)->nullable()->unique();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('defective_quantity')->default(0);
            $table->integer('returned_quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->index('item_code');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};