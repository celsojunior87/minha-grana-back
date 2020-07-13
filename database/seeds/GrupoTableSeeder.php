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
            'tipo_grupo_id' => 1,
            'nome'=>'Receitas',

        ]);
        factory(Grupo::class, 20)->create()->each(function ($grupo) {
            $grupo->items()->save(factory(Item::class)->make(['grupo_id' => $grupo->id]));
        });
    }
}
