<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('cow_fat', 13, 2)->nullable();
            $table->decimal('cow_snf', 13, 2)->nullable();
            $table->decimal('cow_litres', 13, 2)->nullable();
            $table->decimal('cow_amt', 13, 2)->nullable();
            $table->decimal('cow_rate', 13, 2)->nullable();
            $table->decimal('cow_clr', 13, 2)->nullable();

            $table->decimal('mixed_fat', 13, 2)->nullable();
            $table->decimal('mixed_snf', 13, 2)->nullable();
            $table->decimal('mixed_litres', 13, 2)->nullable();
            $table->decimal('mixed_amt', 13, 2)->nullable();
            $table->decimal('mixed_rate', 13, 2)->nullable();
            $table->decimal('mixed_clr', 13, 2)->nullable();

            $table->decimal('buffalo_fat', 13, 2)->nullable();
            $table->decimal('buffalo_snf', 13, 2)->nullable();
            $table->decimal('buffalo_litres', 13, 2)->nullable();
            $table->decimal('buffalo_amt', 13, 2)->nullable();
            $table->decimal('buffalo_rate', 13, 2)->nullable();
            $table->decimal('buffalo_clr', 13, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
