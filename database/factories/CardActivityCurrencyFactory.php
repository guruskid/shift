<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CardActivityCurrency;
use Faker\Generator as Faker;

$factory->define(CardActivityCurrency::class, function (Faker $faker) {
    return [
        //
        'card_activity_id'=> factory(\App\CardActivity::class)->create()->id,
        'currency_id'=> factory(\App\Currency::class)->create()->id
    ];
});
