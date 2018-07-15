<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsWeDontWant extends Migration
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
                $table->dropColumn('payment_terms');
                $table->dropColumn('payment_method');
            });
        };
        
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('selling_price');
                $table->dropColumn('converted_price');
                $table->dropColumn('converted_rate');
                $table->dropColumn('tax');
                $table->dropColumn('tax_rate');
                $table->dropColumn('total');
                $table->dropColumn('discount');
                $table->dropColumn('taxable');
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
