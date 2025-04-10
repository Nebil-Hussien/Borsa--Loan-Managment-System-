<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KnowledgeSkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('skill_taken',255)->default(NULL);
            $table->text('skill_taken_score',255)->nullable();
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
         Schema::dropIfExists('knowledge_skills');
    }
}