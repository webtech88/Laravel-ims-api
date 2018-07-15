<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('created_oauth_client_id')->nullable();
            $table->foreign('created_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('updated_oauth_client_id')->nullable();
            $table->foreign('updated_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('customer_address_hash');
            $table->string('customer_address_company');
            $table->string('customer_address_first_name');
            $table->string('customer_address_last_name');
            $table->string('customer_address_address_line1');
            $table->string('customer_address_address_line2');
            $table->string('customer_address_city');
            $table->string('customer_address_country');
            $table->string('customer_address_postcode');
            $table->string('customer_address_country_code');
            $table->string('customer_address_phone_number');
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
        Schema::dropIfExists('customer_addresses');
    }
}
