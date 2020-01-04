<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*$manager = new User();
        $manager->name = "Hugo Boss";
        $manager->firstname = 'Hugo';
        $manager->lastname = 'Boss';
        $manager->email = 'hugo@boss.at';
        $manager->password = bcrypt('123456');
        $manager->role = 1;
        $manager->organisation_id = 2;
        $manager->save();

        $manager = new User();
        $manager->name = "Franz von Boss";
        $manager->firstname = 'Franz';
        $manager->lastname = 'von Boss';
        $manager->email = 'franz@boss.at';
        $manager->password = bcrypt('123456');
        $manager->role = 0;
        $manager->organisation_id = 2;
        $manager->save();*/

        $manager = new User();
        $manager->name = "Superadministrator";
        $manager->firstname = 'root';
        $manager->lastname = 'root';
        $manager->email = 'root@root.at';
        $manager->password = bcrypt('123456');
        $manager->role = 9;
        $manager->organisation_id = 1;
        $manager->save();


    }
}
