<?php

use Illuminate\Database\Seeder;
use App\Questionnaire;
use App\Period;
use App\Criteria;
use App\Selection;
use Carbon\Carbon;

class QuestionnairesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $m = new Questionnaire();
        $m->user_id = 1;
        $m->title = "Erster Fragebogen 1 von Hugo Boss";
        $m->description = "In diesem Fragebogen werden ganz spezielle Themen abgefragt, die fast geheim sind!";
        $m->question = "Welche Auswahl bevorzugen Sie?";
        $m->inactive =false;
        $m->save();

        $s1 = new Selection();
        $s1->questionnaire_id = $m->id;
        $s1->title = "Media Technologies";
        $s1->sequence = 1;
        $s1->save();

        $s2 = new Selection();
        $s2->questionnaire_id = $m->id;
        $s2->title = "System Technologies";
        $s2->sequence = 2;
        $s2->save();

        $c = new Criteria();
        $c->questionnaire_id = $m->id;
        $c->sequence = 1;
        $c->title = "Video Sequencing";
        $c->info = "Info zu Video Sequencing, Info zu Video Sequencing, Info zu Video Sequencing, Info zu Video Sequencing";
        $c->save();

        $c->selections()->attach($s1->id, ['weight' => 3]);
        $c->selections()->attach($s2->id, ['weight' => 3]);

        $c = new Criteria();
        $c->questionnaire_id = $m->id;
        $c->sequence = 2;
        $c->title = "Embedded Systems";
        $c->info = "Info zu Embedded Systems, Info zu Embedded Systems, Info zu Embedded Systems, Info zu Embedded Systems";
        $c->save();

        $c->selections()->attach($s1->id, ['weight' => 2]);
        $c->selections()->attach($s2->id, ['weight' => 3]);


        $c = new Criteria();
        $c->questionnaire_id = $m->id;
        $c->sequence = 3;
        $c->title = "Microcontroller";
        $c->info = "Micricintrioller with all features!";
        $c->save();

        $c->selections()->attach($s1->id, ['weight' => 3]);
        $c->selections()->attach($s2->id, ['weight' => 1]);

        $c = new Criteria();
        $c->questionnaire_id = $m->id;
        $c->sequence = 3;
        $c->title = "Arduino";
        $c->info = "Arduino Info, Arduino Info, Arduino Info, Arduino Info, Arduino Info, ";
        $c->save();

        $c->selections()->attach($s1->id, ['weight' => 1]);
        $c->selections()->attach($s2->id, ['weight' => 1]);

        $c = new Criteria();
        $c->questionnaire_id = $m->id;
        $c->sequence = 2;
        $c->title = "Adobe Systems";
        $c->info = "Adobe Info, Adobe Info, Adobe Info, Adobe Info, Adobe Info, Adobe Info, Adobe Info, Adobe Info";
        $c->save();

        $c->selections()->attach($s1->id, ['weight' => 1]);
        $c->selections()->attach($s2->id, ['weight' => 1]);

        $p = new Period();
        $p->questionnaire_id=$m->id;
        $p->title = "Erstabfrage Februar 2019";
        $p->startTimeStamp = date('Y-m-d H:i:s',strtotime('2019-12-24 10:15:00'));
        $p->length = 720;
        $end = Carbon::create(2019, 12, 24, 10, 15, 0);
        $p->endTimeStamp = $end->addHours($p->length);

        $p->save();

        $p = new Period();
        $p->questionnaire_id=$m->id;
        $p->title = "Zweitabfrage Dezember 2019";
        $p->startTimeStamp = date('Y-m-d H:i:s',strtotime('2019-12-01 17:35:00'));
        $p->length = 10;

        $end = Carbon::create(2019, 12, 1, 17, 35, 0);
        $p->endTimeStamp = $end->addHours($p->length);

        $p->save();


        $m = new Questionnaire();
        $m->user_id = 2;
        $m->title = "Zweiter Fragebogen 2 von Franz von Boss";
        $m->description = "Regen oder Schnee - was wird passieren? Was denken Sie?";
        $m->question = "Was denken Sie?";
        $m->inactive = false;
        $m->save();

    }
}
