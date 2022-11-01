<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Currency;
use Faker\Generator as Faker;

$factory->define(Currency::class, function (Faker $faker) {
    $name = $faker->currencyCode;
    return [
        //
        'name'=> $name,
        'flag'=> '/public/flags/'.$name.'.jpeg'
    ];
});
