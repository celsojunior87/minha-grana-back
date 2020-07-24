<?php

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
            'grupo_id' => 1,
            'nome' => 'Receita',
            'vl_esperado' => 0,
            'vl_planejado' => 0,
            'vl_recebido' => 0,
            'vl_saldo_esperado' => 0,
            'vl_saldo_realizado' => 0,
            'vl_total' => 0,
        ]);
        Item::create([
            'grupo_id' => 2,
            'nome' => 'Doacao',
            'vl_esperado' => 0,
            'vl_planejado' => 0,
            'vl_recebido' => 0,
            'vl_saldo_esperado' => 0,
            'vl_saldo_realizado' => 0,
            'vl_total' => 0,
        ]);
    }
}
