<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('created_oauth_client_id')->nullable();
            $table->foreign('created_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('updated_oauth_client_id')->nullable();
            $table->foreign('updated_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->integer('billing_address_id')->unsigned();
            $table->foreign('billing_address_id')->references('id')->on('customer_addresses')->onDelete('cascade');
            $table->integer('delivery_address_id')->unsigned();
            $table->foreign('delivery_address_id')->references('id')->on('customer_addresses')->onDelete('cascade');
            $table->string('payment_method')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('order_reference')->unique();
            $table->string('delivery_instructions');
            $table->string('warehouse_instructions');
            $table->string('gift_message');
            $table->string('delivery_method_code');
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
        Schema::dropIfExists('orders');
    }
}
