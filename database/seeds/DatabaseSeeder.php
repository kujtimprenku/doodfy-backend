<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // $this->call(ActivitiesTableSeeder::class);
        // $this->call(CountriesTableSeeder::class);
        // $this->call(CitiesTableSeeder::class);
        // $this->call(CategoriesTableSeeder::class);
        // $this->call(ReactionsTableSeeder::class);
        // $this->call(ActivityUsersTableSeeder::class);
        $this->call(CompanySeeder::class);
    }
}
