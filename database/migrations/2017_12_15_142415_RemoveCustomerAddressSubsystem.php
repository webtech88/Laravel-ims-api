<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCustomerAddressSubsystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_billing_address_id_foreign');
            $table->dropForeign('orders_delivery_address_id_foreign');
            $table->dropColumn('billing_address_id');
            $table->dropColumn('delivery_address_id');
        });
        
        Schema::dropIfExists('customer_addresses');
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
