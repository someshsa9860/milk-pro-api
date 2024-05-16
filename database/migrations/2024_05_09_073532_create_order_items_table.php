<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('type')->nullable();
            $table->decimal('fat', 13, 2)->nullable();
            $table->decimal('snf', 13, 2)->nullable();
            $table->decimal('litres', 13, 2)->nullable();
            $table->decimal('amt', 13, 2)->nullable();
            $table->decimal('rate', 13, 2)->nullable();
            $table->decimal('clr', 13, 2)->nullable();
            $table->string('shift')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
