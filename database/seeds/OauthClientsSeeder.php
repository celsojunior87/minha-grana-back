<?php

use Illuminate\Database\Seeder;
use App\Models\OauthClients;

class OauthClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OauthClients::create([
            'id' => 3,
            'user_id' => null,
            'name' => 'Laravel Personal Access Client',
            'secret' => 'kImGVdyPhISA17TxnQjTtuqzVLQk0weyq76LbTgj',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
            'created_at' => '2020-06-02 02:31:24.000',
            'updated_at' => '2020-06-02 02:31:24.000'
        ]);
    }
}
