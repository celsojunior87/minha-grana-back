<?php

use Illuminate\Database\Seeder;
use App\Models\Produto;

class ProdutoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Produto::Create([
            'nome' => 'Licença de Software Fiscal Manager',
            'modelo' => 'LIS-FM',
            'categoria' => 'software',
            'valor' => 179.59
        ]);

        Produto::Create([
            'nome' => 'Tablet Samsung Galaxy',
            'modelo' => 'TAB E 9.6 SM-T650',
            'categoria' => 'hardware',
            'valor' => 2899.99
        ]);

        Produto::Create([
            'nome' => 'Suporte Metalico Tablet Bemacash 9.6 Pol',
            'modelo' => 'POL-499',
            'categoria' => 'hardware',
            'valor' => 99.35
        ]);

        Produto::Create([
            'nome' => 'Gaveta Dinheiro',
            'modelo' => 'GD-56 Preta',
            'categoria' => 'hardware',
            'valor' => 198.00
        ]);

        Produto::Create([
            'nome' => 'MP-4200',
            'modelo' => 'TH ETHERNET BR 10080',
            'categoria' => 'hardware',
            'valor' => 698.80
        ]);

        Produto::Create([
            'nome' => 'Licenciamento Bemacash Vestuário',
            'modelo' => 'LSB-V',
            'categoria' => 'software',
            'valor' => 589.20
        ]);
    }
}
