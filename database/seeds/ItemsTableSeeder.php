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
            'vl_esperado' => 0000,
            'vl_planejado' => 0001,
            'vl_recebido' => 000,
            'vl_saldo_esperado' => 000,
            'vl_saldo_realizado' => 000,
            'vl_total' => 000,
            'status' => 0,
        ]);
        Item::create([
            'grupo_id' => 2,
            'nome' => 'Doacao',
            'vl_esperado' => 0000,
            'vl_planejado' => 0001,
            'vl_recebido' => 000,
            'vl_saldo_esperado' => 000,
            'vl_saldo_realizado' => 000,
            'vl_total' => 000,
            'status' => 0,
        ]);

        Item::create([
            'grupo_id' => 3,
            'nome' => 'Despesas',
            'vl_esperado' => 0000,
            'vl_planejado' => 0001,
            'vl_recebido' => 000,
            'vl_saldo_esperado' => 000,
            'vl_saldo_realizado' => 000,
            'vl_total' => 000,
            'status' => 0,
        ]);
        Item::create([
            'grupo_id' => 4,
            'nome' => 'Ajustes',
            'vl_esperado' => 0000,
            'vl_planejado' => 0001,
            'vl_recebido' => 000,
            'vl_saldo_esperado' => 000,
            'vl_saldo_realizado' => 000,
            'vl_total' => 000,
            'status' => 0,
        ]);

    }
}
