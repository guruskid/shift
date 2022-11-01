<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PaymentMedium;
use Faker\Generator as Faker;

$factory->define(PaymentMedium::class, function (Faker $faker) {
    return [
        //

        'name'=> $faker->randomElement(['E-Code', 'Physical', 'Large Card', 'Small Card']),
        'currency_id'=> factory(\App\Currency::class)->create()->id
    ];
});
