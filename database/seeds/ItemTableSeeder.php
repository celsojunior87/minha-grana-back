<?php

use Illuminate\Database\Seeder;


class TipoGrupoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TipoGrupo::create([
            'nome'=>'Receitas',

        ]);
        \App\Models\TipoGrupo::create([
            'nome'=>'Dividas',

        ]);
        \App\Models\TipoGrupo::create([
            'nome'=>'Economias',

        ]);
        \App\Models\TipoGrupo::create([
            'nome'=>'Despesas',

        ]);
    }
}
