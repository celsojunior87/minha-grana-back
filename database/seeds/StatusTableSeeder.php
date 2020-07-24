<?php

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::create([
            'id' => 1,
            'nome' => 'Ajuste',
        ]);
        Status::create([
            'id' => 2,
            'nome' => 'feito',
        ]);
        Status::create([
            'id' => 3,
            'nome' => 'Aguardando',
        ]);

    }
}
