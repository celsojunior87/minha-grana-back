<?php

use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \App\Models\Cliente::Create([
            'nome' => 'Ronaldo Campos dias',
            'cpf' => '00700700757',
            'email' => 'ronaldo@mariamelia.com.br',
            'telefone' => '6132310203',
            'celular' => '61981355287',
            'tipo_cliente' => 0,
            'observacao' => 'Um otimo cliente , os seus pagamentos são a vista'
        ]);

        \App\Models\Cliente::Create([
            'nome' => 'João da silva',
            'cpf' => '01752636120',
            'email' => 'joao@bol.com.br',
            'telefone' => '6120215859',
            'celular' => '61981200236',
            'tipo_cliente' => 1,
            'observacao' => 'Problema com os depositos dos cheques'
        ]);

        \App\Models\Cliente::Create([
            'nome' => 'Mariana campos dias',
            'cpf' => '15203269832',
            'email' => 'maria@uol.com.br',
            'telefone' => '6130362201',
            'celular' => '6193639698',
            'tipo_cliente' => 2,
            'observacao' => 'Cliente problemático com uma postura lamentavel'
        ]);
        \App\Models\Cliente::Create([
            'nome' => 'Jessica da Silva',
            'cpf' => '05869632100',
            'email' => 'jessica@bol.com.br',
            'telefone' => '6196963636',
            'celular' => '61981103636',
            'tipo_cliente' => 3,
            'observacao' => 'Cliente com histórico de inadiplencia , vender apenas a vista'
        ]);
    }
}
