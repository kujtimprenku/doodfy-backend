<?php

use Illuminate\Database\Seeder;

class ActivityUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\ActivityUser::class, 500)->create();
    }
}
