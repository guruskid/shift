<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CurrencyPaymentMedium;
use Faker\Generator as Faker;

$factory->define(CurrencyPaymentMedium::class, function (Faker $faker) {
    return [
        //
        'currency_id'=> factory(\App\Currency::class)->create()->id,
        'payment_media_id'=> factory(\App\PaymentMedium::class)->create()->id
    ];
});
