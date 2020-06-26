<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Admin',
            'email'=>'admin@user.com',
            'respostas'=>'Começar a investir',
            'password'=> bcrypt('123456')
        ]);
    }
}
