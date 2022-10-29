<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Card;
use Faker\Generator as Faker;

$factory->define(Card::class, function (Faker $faker) {

    $cardNames = ['Amazon', 'Steam', 'Walmart', 'iTunes', 'Play Store'];
    return [
        //
        'name'=> $faker->randomElement($cardNames). ' Card',
        'image'=> '/public/images/card.png',
        'is_crypto'=> 0,
        'buyable'=> $faker->randomElement([true, false]),
        'sellable'=> $faker->randomElement([true, false])
    ];
});
