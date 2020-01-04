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
        $this->call(OrganisationsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
       // $this->call(QuestionnairesTableSeeder::class);

    }
}
