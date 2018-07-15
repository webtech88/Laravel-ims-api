<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourierDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('created_oauth_client_id')->nullable();
            $table->foreign('created_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('updated_oauth_client_id')->nullable();
            $table->foreign('updated_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('shipping_meta', 5000);
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
        Schema::dropIfExists('courier_deliveries');
    }
}
