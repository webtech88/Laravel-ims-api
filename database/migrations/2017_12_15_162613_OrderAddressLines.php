<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderAddressLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function($table) {
            $table->renameColumn('customer_billing_line1', 'customer_billing_address_line1');
            $table->renameColumn('customer_billing_line2', 'customer_billing_address_line2');
            $table->renameColumn('customer_delivery_line1', 'customer_delivery_address_line1');
            $table->renameColumn('customer_delivery_line2', 'customer_delivery_address_line2');
        });
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
