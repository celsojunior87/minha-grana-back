<?php

use Illuminate\Database\Seeder;
use App\Models\Grupo;
use App\Models\Item;

class GrupoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 1,
            'nome' => 'Receitas',
            'data' => '2020-07-20'
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Doação',
            'data' => '2020-07-20'
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Economias',
            'data' => '2020-07-20'
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Casa',
            'data' => '2020-07-20'
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Dívidas',
            'data' => '2020-07-20'
        ]);
    }
}
