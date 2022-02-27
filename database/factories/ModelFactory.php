<?php

use Faker\Generator as Faker;


$factory->define(App\Country::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'code' => $faker->numberBetween($min=10000,$max=72000)
    ];
});
$factory->define(App\City::class, function (Faker $faker) {
    return [
        'country_id' => $faker->numberBetween($min=1,$max=20),
        'name' => $faker->city,
        'post_code' => $faker->numberBetween($min=10000,$max=72000),
        'lat' => $faker->numberBetween($min=10000,$max=72000),
        'lon' => $faker->numberBetween($min=10000,$max=72000),

    ];
});
$factory->define(App\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'image' => $faker->imageUrl($width = 600, $height = 600)
    ];
});

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        'gender'=>$faker->word,
        'address'=>$faker->word,
        'website'=>$faker->word,
        'city_id'=>$faker->numberBetween($min=1,$max=100),
        'profile_image'=>$faker->imageUrl($width = 300, $height = 300),
        'xp_points'=>$faker->numberBetween($min=1,$max=100),
        'is_active'=>$faker->numberBetween($min=0,$max=1),
        'role_id'=>$faker->numberBetween($min=0,$max=1),
        'birth_date'=>$faker->dateTime($min = 'now', $timezone = null),
        'remember_token' => str_random(10)
    ];
});

$factory->define(App\Activity::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween($min=1,$max=300),
        'title'=>$faker->sentence(5),
        'city_id' => $faker->numberBetween($min=1,$max=50),
        'subcategory_id' => $faker->numberBetween($min=1,$max=50),
        'lat' => $faker->numberBetween($min=1,$max=1000),
        'lon' => $faker->numberBetween($min=1,$max=1000),
        'address' => $faker->city,
        'equipments_needed' => $faker->sentence(2),
        'prerequisites' => $faker->sentence(4),
        'min_persons' => $faker->numberBetween($min=1,$max=10),
        'max_persons' => $faker->numberBetween($min=10,$max=10),
        'achievements'=>$faker->sentence(4),
        'description'=>$faker->sentence(60),
        'has_xp' => $faker->numberBetween($min=0,$max=1),
        'start_date'=>$faker->dateTime($min = 'now', $timezone = null),
        'end_date'=>$faker->dateTime($min = 'now', $timezone = null),
        'image'=>$faker->imageUrl($width = 400, $height = 400)
    ];
});

$factory->define(App\ActivityUser::class, function (Faker $faker) {
    return [
        'activity_id' => $faker->numberBetween($min=1,$max=1000),
        'user_id' => $faker->numberBetween($min=1,$max=300),
        'has_joined' => $faker->numberBetween($min=0,$max=1),
        'has_saved'=>$faker->numberBetween($min=0,$max=1),
        'xp_points'=>$faker->numberBetween($min=10,$max=50)
    ];
});


$factory->define(App\Company::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween($min=1,$max=100),
        'city_id' => $faker->numberBetween($min=1,$max=200),
        'country_id' => null,
        'firm'=>$faker->word,
        'addition'=>$faker->sentence(10),
        'street'=>$faker->word,
        'website'=>$faker->word,
    ];
});

$factory->define(App\Reaction::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween($min=1,$max=300),
        'activity_id' => $faker->numberBetween($min=1,$max=1000),
        'comment' => $faker->sentence(10),
    ];
});
