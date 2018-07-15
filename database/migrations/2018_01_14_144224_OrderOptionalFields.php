<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderOptionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function($table) {
            $table->string('delivery_date')->after('order_status_id')->nullable();
            $table->string('customer_billing_country')->after('customer_billing_country_code');
            $table->string('customer_delivery_country')->after('customer_delivery_country_code');
            
            $table->string('customer_billing_address_line2')->nullable()->change();
            $table->string('customer_delivery_address_line2')->nullable()->change();
        });
        
        Schema::table('customers', function(Blueprint $table)
        {
            $table->dropUnique('customers_customer_reference_unique');
            $table->string('customer_reference')->nullable()->change();
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
