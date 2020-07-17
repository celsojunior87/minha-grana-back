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
        \App\Models\Grupo::create([
            'user_id' => 1,
            'tipo_grupo_id' => 1,
            'nome' => 'Receitas',
            'data' => '2020-07-20'

        ]);
        factory(Grupo::class, 20)->create()->each(function ($grupo) {
            $grupo->items()->save(factory(Item::class)->make(['grupo_id' => $grupo->id]));
        });
    }
}
