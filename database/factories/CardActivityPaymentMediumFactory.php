<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CardActivityPaymentMedium;
use Faker\Generator as Faker;

$factory->define(CardActivityPaymentMedium::class, function (Faker $faker) {
    return [
        //
        'card_activity_id'=> factory(\App\CardActivity::class)->create()->id,
        'payment_media_id'=> factory(\App\PaymentMedium::class)->create()->id,
        'payment_range_settings'=> "{'min': 3, 'max': 20, 'amount':300}, {'min': 21, 'max': 100, 'amount':200}"
    ];
});
