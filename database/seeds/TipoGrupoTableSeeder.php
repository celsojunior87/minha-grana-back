<?php

use Illuminate\Database\Seeder;
use App\Models\TipoGrupo;


class TipoGrupoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoGrupo::create([
            'nome'=>'Receitas',
        ]);
        TipoGrupo::create([
            'nome'=>'Despesas',
        ]);
    }
}
