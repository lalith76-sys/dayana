<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Default settings
        DB::table('general_settings')->insert([
            ['key' => 'company_name', 'value' => 'Dayana Enterprises', 'description' => 'Company Name'],
            ['key' => 'company_address', 'value' => 'Colombo, Sri Lanka', 'description' => 'Company Address'],
            ['key' => 'company_phone', 'value' => '+94 11 2345678', 'description' => 'Company Phone'],
            ['key' => 'company_email', 'value' => 'info@dayanaenterprises.lk', 'description' => 'Company Email'],
            ['key' => 'currency', 'value' => 'LKR', 'description' => 'Currency'],
            ['key' => 'tax_rate', 'value' => '0', 'description' => 'Tax Rate (%)'],
            ['key' => 'invoice_prefix', 'value' => 'INV-', 'description' => 'Invoice Prefix'],
            ['key' => 'po_prefix', 'value' => 'PO-', 'description' => 'Purchase Order Prefix'],
            ['key' => 'grn_prefix', 'value' => 'GRN-', 'description' => 'GRN Prefix'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};