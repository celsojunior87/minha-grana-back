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
            $table->integer('ordenacao')->nullable();
            $table->decimal('vl_esperado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_planejado', 20, 2)->nullable()->default(0.0);
            $table->decimal('vl_recebido', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_saldo_esperado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_saldo_realizado', 20, 2)->nullable()->default(0.00);
            $table->decimal('vl_total', 20, 2)->nullable()->default(0.00);
            $table->date('data')->nullable();
            $table->string('vl_saldo_inicial')->nullable()->default(0.00);
            $table->string('vl_gasto')->nullable()->default(0.00);
            $table->string('vl_total_objetivo')->nullable()->default(0.00);
            $table->decimal('juros_multas', 20, 2)->nullable()->default(0.00);
            $table->decimal('pagamento_minimo', 20, 2)->nullable()->default(0.00);
            $table->string('transferencia_id')->nullable();
            $table->unsignedInteger('tipo_item_id')->nullable();
            $table->foreign('tipo_item_id')
                ->references('id')
                ->on('tipo_item')
                ->onDelete('cascade');
            $table->unsignedInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')
                ->references('id')
                ->on('grupo')
                ->onDelete('cascade');

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
