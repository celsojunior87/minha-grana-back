<?php

use App\Models\Historico;
use Illuminate\Database\Seeder;

class HistoricoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Historico::Create([
            'status' => 'Pedido Confirmado'
        ]);

        Historico::Create([
            'status' => 'Pagamento Aprovado'
        ]);
        
        Historico::Create([
            'status' => 'Pedido Processado'
        ]);

        Historico::Create([
            'status' => 'Pedido Faturado'
        ]);

        Historico::Create([
            'status' => 'Pedido Entrege'
        ]);
    }
}
