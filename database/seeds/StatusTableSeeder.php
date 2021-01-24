<?php

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Antes era AJUSTE
         */
        Status::create([
            'id' => 1,
            'nome' => 'Ajustar',
        ]);
        /**
         * Antes era FEITO
         */
        Status::create([
            'id' => 2,
            'nome' => 'Concluída',
        ]);
        Status::create([
            'id' => 3,
            'nome' => 'Aguardando',
        ]);

    }
}
