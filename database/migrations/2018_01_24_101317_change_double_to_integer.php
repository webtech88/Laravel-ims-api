<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDoubleToInteger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( Schema::hasTable('order_items') )
        {
            Schema::table('order_items', function ($table)
            {
                $table->integer('items_ordered')->change();
                $table->integer('items_reserved')->change();
            });
        }

        if ( Schema::hasTable('order_returns') )
        {
            Schema::table('order_returns', function ($table)
            {
                $table->integer('items_in_stock')->change();
                $table->integer('items_shipped')->change();
                $table->integer('items_returned')->change();
            });
        }

        if ( Schema::hasTable('order_shipments') )
        {
            Schema::table('order_shipments', function ($table)
            {
                $table->integer('items_in_stock')->change();
                $table->integer('items_shipped')->change();
            });
        }

        if ( Schema::hasTable('product_inventories') )
        {
            Schema::table('product_inventories', function ($table)
            {
                $table->integer('items_in_stock')->change();
                $table->integer('items_supplied')->change();
                $table->integer('items_reserved')->change();
                $table->integer('items_shipped')->change();
                $table->integer('items_returned')->change();
                $table->integer('items_lost_stolen')->change();
            });
        }

        if ( Schema::hasTable('product_inventory_adjustments') )
        {
            Schema::table('product_inventory_adjustments', function ($table)
            {
                $table->integer('items_in_stock')->change();
                $table->integer('items_supplied')->change();
                $table->integer('items_reserved')->change();
                $table->integer('items_shipped')->change();
                $table->integer('items_returned')->change();
                $table->integer('items_lost_stolen')->change();
            });
        }

        if ( Schema::hasTable('supplies') )
        {
            Schema::table('supplies', function ($table)
            {
                $table->integer('items_in_stock')->change();
                $table->integer('items_supplied')->change();
            });
        }
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
