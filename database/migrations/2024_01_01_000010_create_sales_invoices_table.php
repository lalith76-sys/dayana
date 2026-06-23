<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->date('date');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('sales_type', ['cash', 'credit'])->default('cash');
            $table->string('payment_method', 50)->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'on_hold'])->default('pending');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_hold')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->index('invoice_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};