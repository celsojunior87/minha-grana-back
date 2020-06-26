<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(OauthClientsSeeder::class);
        //$this->call(ProdutoTableSeeder::class);
//        $this->call(HistoricoTableSeeder::class);
        //$this->call(ProdutosPedidosTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
//        $this->call(ClientesSeeder::class);
//        $this->call(FornecedoresSeeder::class);
    }
}
