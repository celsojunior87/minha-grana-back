<?php

use Illuminate\Database\Seeder;
use App\Models\TipoItem;


class TipoItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoItem::create([
            'nome'=>'Economia',
        ]);
        TipoItem::create([
            'nome'=>'Dívida',
        ]);
    }
}
