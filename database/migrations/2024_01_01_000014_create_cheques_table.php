<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('cheque_number', 100);
            $table->string('bank_name', 200);
            $table->enum('type', ['received', 'issued']);
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'deposited', 'cleared', 'returned', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('returned_date')->nullable();
            $table->text('return_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->index('cheque_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};