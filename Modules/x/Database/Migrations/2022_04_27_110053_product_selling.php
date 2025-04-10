<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductSelling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('product_selling', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('product_item',255)->default(NULL);
            $table->float('product_item_price')->default(0.00);
            $table->bigInteger('client_id')->unsigned();

        
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_selling');

    }
}