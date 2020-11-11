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
        $this->call(TipoGrupoTableSeeder::class);
        $this->call(GrupoTableSeeder::class);
        $this->call(TipoItemTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(StatusTableSeeder::class);

    }
}
