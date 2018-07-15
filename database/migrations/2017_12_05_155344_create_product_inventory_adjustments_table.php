<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductInventoryAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('product_inventory_adjustments', function (Blueprint $table) {
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
            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->integer('supply_id')->unsigned()->nullable();
            $table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
            $table->integer('order_item_id')->unsigned()->nullable();
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->integer('order_shipment_id')->unsigned()->nullable();
            $table->foreign('order_shipment_id')->references('id')->on('order_shipments')->onDelete('cascade');
            $table->integer('order_return_id')->unsigned()->nullable();
            $table->foreign('order_return_id')->references('id')->on('order_returns')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->double('items_in_stock', 15, 2)->default(0)->comment = 'Increment/decrement total warehouse remain of specified product';
            $table->double('items_supplied', 15, 2)->default(0)->comment = 'Increment/decrement of supplied items';
            $table->double('items_reserved', 15, 2)->default(0)->comment = 'Increment/decrement of reserved items';
            $table->double('items_shipped', 15, 2)->default(0)->comment = 'Increment/decrement of shipped items';
            $table->double('items_returned', 15, 2)->default(0)->comment = 'Increment/decrement of returned items';
            $table->double('items_lost_stolen', 15, 2)->default(0)->comment = 'Increment/decrement of lost/stolen items';
            $table->softDeletes();
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
        Schema::dropIfExists('product_inventory_adjustments');
    }
}
