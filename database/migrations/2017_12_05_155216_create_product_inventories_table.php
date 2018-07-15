<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->increments('id')->unsigned();
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
            $table->double('items_in_stock', 15, 2)->default(0)->comment = 'Total warehouse remain of specified product';
            $table->double('items_supplied', 15, 2)->default(0)->comment = 'Amount of supplied items';
            $table->double('items_reserved', 15, 2)->default(0)->comment = 'Amount of reserved items';
            $table->double('items_shipped', 15, 2)->default(0)->comment = 'Amount of shipped items';
            $table->double('items_returned', 15, 2)->default(0)->comment = 'Amount of returned items';
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
        Schema::dropIfExists('product_inventories');
    }
}
