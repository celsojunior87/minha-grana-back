<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Grupo;
use App\Helper\Color;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Grupo::class, function (Faker $faker) {
    return [
        'nome' => $faker->word,
        'user_id' => 1,
        'tipo_grupo_id' =>2,
        'data' => now()
    ];
});
