<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAparelhos extends Migration
{
    public $tableName = 'aparelhos';

    /**
     * Run the migrations.
     * @table aparelhos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->text('identificador')->nullable();
            # Tipo: 1 - Android | 2 - iOS
            $table->string('tipo', 1)->nullable();
            $table->unsignedInteger('users_id');
            $table->timestamps();
            
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aparelhos', function (Blueprint $table) {
            //
        });
    }
}
