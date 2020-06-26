<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('respostas')->unique();
            $table->text('link_facebook')->nullable();
            $table->string('rede_social')->nullable();
            $table->boolean('is_rede_social_publico')->default(false);
            //$table->string('cpf',11)->unique();
            $table->string('avatar')->nullable();
            //$table->string('sexo')->nullable();
            //$table->string('telefone');
            //$table->string('celular');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
