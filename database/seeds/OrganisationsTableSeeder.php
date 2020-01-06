<?php

use Illuminate\Database\Seeder;
use App\Organisation;

class OrganisationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {
            $o = new Organisation();
            $o->title = "htlkrems";
            $o->info = 'HTL Krems - Abteilung für Informationstechnologie';
            $o->inactive = false;
            $o->save();

            $o = new Organisation();
            $o->title = "brno";
            $o->info = 'Střední škola informatiky, poštovnictví a finančnictví Brno';
            $o->inactive = false;
            $o->save();

        }
    }
}
