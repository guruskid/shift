<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CardActivity;
use Faker\Generator as Faker;

$factory->define(CardActivity::class, function (Faker $faker) {
    return [
        //
        'activity'=> $faker->randomElement(['Buying', 'Selling']),
        'card_id'=> factory(\App\Card::class)->create()->id
    ];
});
