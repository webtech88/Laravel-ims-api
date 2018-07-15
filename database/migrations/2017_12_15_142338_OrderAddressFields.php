<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderAddressFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('customer_billing_company');
                $table->string('customer_billing_first_name');
                $table->string('customer_billing_last_name');
                $table->string('customer_billing_line1');
                $table->string('customer_billing_line2');
                $table->string('customer_billing_city');
                $table->string('customer_billing_country');
                $table->string('customer_billing_postcode');
                $table->string('customer_billing_country_code');
                $table->string('customer_billing_phone_number');

                $table->string('customer_delivery_company');
                $table->string('customer_delivery_first_name');
                $table->string('customer_delivery_last_name');
                $table->string('customer_delivery_line1');
                $table->string('customer_delivery_line2');
                $table->string('customer_delivery_city');
                $table->string('customer_delivery_country');
                $table->string('customer_delivery_postcode');
                $table->string('customer_delivery_country_code');
                $table->string('customer_delivery_phone_number');
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
