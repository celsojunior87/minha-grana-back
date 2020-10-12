<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableItemTransferencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_item_transferencia', function (Blueprint $table) {
            $table->id();
            $table->decimal('vl_transferencia', 20, 2)->nullable()->default(0.00);
            $table->unsignedInteger('item_id_de')->nullable();
            $table->foreign('item_id_de')
                ->references('id')
                ->on('item')
                ->onDelete('cascade');
            $table->unsignedInteger('item_id_para')->nullable();
            $table->foreign('item_id_para')
                ->references('id')
                ->on('item')
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
        Schema::dropIfExists('table_item_transferencia');
    }
}
