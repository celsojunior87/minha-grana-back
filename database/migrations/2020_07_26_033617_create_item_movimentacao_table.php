<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemMovimentacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_movimentacao', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descricao');
            $table->integer('ordenacao')->nullable();
            $table->decimal('vl_planejado', 20, 2)->nullable()->default(0.0);
            $table->decimal('vl_saldo_esperado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_realizado', 20, 2)->nullable()->default(0.00);
            $table->date('data')->nullable();
            $table->unsignedInteger('item_id')->nullable();
            $table->foreign('item_id')
                ->references('id')
                ->on('item');
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
        Schema::dropIfExists('item_movimentacao');
    }
}
