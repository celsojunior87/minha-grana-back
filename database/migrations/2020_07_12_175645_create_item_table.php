<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->decimal('vl_esperado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_planejado', 20, 2)->nullable()->default(0.0);
            $table->decimal('vl_recebido', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_saldo_esperado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_saldo_realizado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_total', 20, 2)->nullable()->default(0.00);
            $table->date('data')->nullable();
            $table->string('gasto')->nullable()->default(0.00);;
            $table->boolean('status')->nullable();
            $table->unsignedInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')
                ->references('id')
                ->on('grupo');
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
        Schema::dropIfExists('item');
    }
}
