<?php

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
{

    const TOTAL = 4;
    const ADMIN = 1;
    const CLIENTE = 2;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->admin();
//        $this->cliente();
    }

    public function toAll()
    {
        return [1];
    }

    public function admin()
    {
        $arr = range(2, self::TOTAL);
        $this->makePermission($arr, self::ADMIN);
    }

    public function cliente()
    {
//        $arr = [8, 10, 12, 14, 16, 17, 18, 19, 20, 21, 22, 25, 24, 28];P
//        $this->makePermission($arr, self::CLIENTE);
    }

    public function makePermission($arr, $role_id)
    {
        $arr = array_merge($this->toAll(), $arr);
        foreach ($arr as $permission) {
            DB::table('role_has_permissions')->insert(
                [
                    'permission_id' => $permission,
                    'role_id' => $role_id
                ]
            );
        }
    }
}
