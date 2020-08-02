<?php

use Illuminate\Database\Seeder;
use App\Models\Grupo;
use App\Models\Item;
use Carbon\Carbon;

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
            'data' => Carbon::now()
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Doação',
            'data' => Carbon::now()
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Economias',
            'data' => Carbon::now()
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Casa',
            'data' => Carbon::now()
        ]);
        Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 2,
            'nome' => 'Dívidas',
            'data' => Carbon::now()
        ]);
    }
}
