<?php

use Illuminate\Database\Seeder;

class FornecedoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \App\Models\Fornecedores::Create([
            'nome' => 'Fornecedor de Leite',
            'cnpj' => '11501555000133',
            'email' => 'fornecedordeleite@gmail.com',
            'telefone' => '6199880102',
            'celular' => '61981355287',
            'inscricao_estadual' => '1561561561561',
            'observacao' => 'fornece o leite',
            'responsavel' => 'Marivaldo'
        ]);
        \App\Models\Fornecedores::Create([
            'nome' => 'Fornecedor de Trigo',
            'cnpj' => '23272162000184',
            'email' => 'fornecedordetrigo@gmail.com',
            'telefone' => '6188229901',
            'celular' => '6198225955',
            'inscricao_estadual' => '01215956148541',
            'observacao' => 'teste 2',
            'responsavel' => 'Claudionor'
        ]);
        \App\Models\Fornecedores::Create([
            'nome' => 'Fornecedor de Chocolate',
            'cnpj' => '56015432000120',
            'email' => 'fornecedordechocolate@gmail.com',
            'telefone' => '6120215582',
            'celular' => '6198135025',
            'inscricao_estadual' => '010203040506',
            'observacao' => 'teste 2',
            'responsavel' => 'João do Chocolate'
        ]);
    }
}
