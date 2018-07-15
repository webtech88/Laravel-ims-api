<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsLostStolen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('product_inventories')) {
            Schema::table('product_inventories', function (Blueprint $table) {
                $table->double('items_lost_stolen', 15, 2)->default(0)->after('items_returned')->comment = 'Amount of product that has been lost or stolen';
            });
        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
