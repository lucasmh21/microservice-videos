<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Video::class, function (Faker $faker) {
    return [
        'title' => $faker->text(),
        'description' => $faker->text(),
        'year_launched' => $faker->numberBetween(1895,2022),
        'opened' => rand(1,4) > 1,
        'rating' => substr($faker->text(), 0, 2),
        'duration' => rand(10, 300)
    ];
});
