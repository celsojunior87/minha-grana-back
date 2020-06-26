<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(
            [
                'name' => 'admin',
                'guard_name' => 'api',
                'public_name' => 'Administrador'
            ]
        );
        DB::table('roles')->insert(
            [
                'name' => 'cliente',
                'guard_name' => 'api',
                'public_name' => 'Cliente'
            ]
        );
    }
}
