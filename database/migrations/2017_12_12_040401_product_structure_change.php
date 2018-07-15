<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductStructureChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
//                $table->dropForeign('manufacturer_id');
//                Schema::disableForeignKeyConstraints();
                $table->string('title')->nullable()->change();
                $table->integer('manufacturer_id')->nullable()->unsigned()->change();
//                Schema::enableForeignKeyConstraints();
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
