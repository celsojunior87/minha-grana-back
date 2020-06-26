<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            1 => 'home',
            2 => 'dashboard',

            3 => 'configuration',
            4 => 'configuration-perfil',
            5 => 'configuration-permissao',
            6 => 'configuration-acao',
        ];

        foreach ($permission as $permission) {
            DB::table('permissions')->insert(
                [
                    'name' => $permission,
                    'guard_name' => 'api'
                ]
            );
        }
    }
}
