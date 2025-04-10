<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BussineessSector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('bussineess_sector', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('bussiness_sector_name',255)->default(NULL);
            $table->text('bussiness_sector_score',255)->nullable();
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
         Schema::dropIfExists('bussineess_sector');
    }
}