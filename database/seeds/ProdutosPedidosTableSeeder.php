<?php

use App\Models\Pedido;
use Illuminate\Database\Seeder;

class ProdutosPedidosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Pedido::Create([
            'status_id' => 5,
            'data' => '2019-08-15 11:55:19',
            'total' => 2899.99,
            'cnpj' => '4000101',
            'estado' => 'São Paulo',
            'cidade' => 'Campinas',
            'pais' => 'Brasil',
            'telefone' => '(19) 2668-8540',
            'comentario' => '',
            'bairro' => 'Vila Costa e Silva',
            'cep' => '13081-470',
            'endereco' => 'Rua dos Parecis',
            'numero' => 764,
            'executivo_vendas' => 'Marcelo',
            'nfe' => '797581',
            'nfe_data' => '2019-10-04'
        ]);
        Pedido::find(1)->produtos()->attach([
            2 => ['valor_unitario' => 2899.99,  'quantidade' => 1]
        ]);
        Pedido::find(1)->update([
            'created_at' => '2019-08-10 18:05:10',
            'updated_at' => '2019-08-10 18:05:10',
        ]);


        Pedido::Create([
            'status_id' => 1,
            'data' => '2019-09-27 15:05:10',
            'total' => 575.59,
            'cnpj' => '484077',
            'estado' => 'São Paulo',
            'cidade' => 'Campinas',
            'pais' => 'Brasil',
            'telefone' => '(19) 2668-8540',
            'comentario' => '',
            'bairro' => 'Vila Costa e Silva',
            'cep' => '13081-470',
            'endereco' => 'Rua dos Parecis',
            'numero' => 764,
            'executivo_vendas' => 'Marcelo',
            'nfe' => '2534',
            'nfe_data' => '2019-09-25 11:00:10'
        ]);
        Pedido::find(2)->produtos()->attach([
            1 => ['valor_unitario' => 179.59, 'quantidade' => 1],
            4 => ['valor_unitario' => 198.00, 'quantidade' => 2]
        ]);

        Pedido::find(2)->update([
            'created_at' => '2019-09-25 11:00:10',
            'updated_at' => '2019-09-25 11:00:10'
        ]);

        Pedido::Create([
            'status_id' => 3,
            'data' => '2019-10-01 16:15:11',
            'total' => 1176.44,
            'cnpj' => '484077840',
            'estado' => 'São Paulo',
            'cidade' => 'Campinas',
            'pais' => 'Brasil',
            'telefone' => '(19) 2668-8540',
            'comentario' => '',
            'bairro' => 'Vila Costa e Silva',
            'cep' => '13081-470',
            'endereco' => 'Rua dos Parecis',
            'numero' => 764,
            'executivo_vendas' => 'Ana',
            'nfe' => '235',
            'nfe_data' => '2019-09-25 11:00:10'
        ]);
        Pedido::find(3)->produtos()->attach([
            3 => ['valor_unitario' => 99.35,  'quantidade' => 3],
            5 => ['valor_unitario' => 698.80, 'quantidade' => 1],
            1 => ['valor_unitario' => 179.59, 'quantidade' => 1],
        ]);
        Pedido::find(3)->update([
            'created_at' => '2019-09-29 15:15:11',
            'updated_at' => '2019-09-29 15:15:11',
        ]);
    }
}
