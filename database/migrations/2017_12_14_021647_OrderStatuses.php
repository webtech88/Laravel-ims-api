<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderStatuses extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->increments('id')->unsigned();

            $table->unsignedInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('updated_oauth_client_id')->nullable();
            $table->foreign('updated_oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        DB::table('order_statuses')->insert(
        [
            ['title' => 'Backorder', 'description' => 'Has items with stock quantity of less than required.'],
            ['title' => 'Confirmed', 'description' => 'Everything seems just fine.'],
            ['title' => 'Picking', 'description' => 'The search for pallet is on!'],
            ['title' => 'Packing', 'description' => 'In loving hands of packing staff.'],
            ['title' => 'Packed', 'description' => 'Waiting for pick up.'],
            ['title' => 'Dated Delivery', 'description' => '???'],
            ['title' => 'Dispatched', 'description' => 'Retrieved from warehouse by courier company.'],
            ['title' => 'Delivered', 'description' => 'Happy customer is holding the package.'],
        ]
        );

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('order_status_id')->nullable()->unsigned();
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('order_statuses');
    }

}
