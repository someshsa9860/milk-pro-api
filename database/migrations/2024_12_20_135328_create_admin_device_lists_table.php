<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminDeviceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_device_lists', function (Blueprint $table) {
            $table->id();
            $table->string('full_device_name')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->boolean('block')->nullable();
            $table->text('ip_addresses')->nullable();
            $table->string('device_id')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('last_accessed')->nullable();
            $table->dateTime('last_logout_at')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->string('uuid')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_model')->nullable();
            $table->text('session_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admin_users')->nullOnDelete();

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
        Schema::dropIfExists('admin_device_lists');
    }
}
