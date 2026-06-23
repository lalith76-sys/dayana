<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default expense types
        DB::table('expense_types')->insert([
            ['name' => 'Rent', 'description' => 'Rental expenses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salary', 'description' => 'Employee salaries', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Electricity', 'description' => 'Electricity bills', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Water', 'description' => 'Water bills', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transport', 'description' => 'Transportation expenses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Miscellaneous', 'description' => 'Other expenses', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_types');
    }
};