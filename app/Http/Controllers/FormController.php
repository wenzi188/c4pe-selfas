<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Questionnaire;
use App\Criteria;
use Illuminate\Support\Facades\DB;
use App\Selection;
use App\Token;
use App\Assessment;
use App\Period;
use App\User;
use Lang;

class FormController extends Controller
{
    //
    public function index(Request $request, $organisation_id,  $questionnaire_id, $periodId)
    {
        $questionnaire = Questionnaire::find($questionnaire_id);

        // check if organisation fits questionnaire organisation
        $user = User::find($questionnaire->user_id);
        if($organisation_id != $user->organisation_id)
            return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.paramsNotOk')]);

        $period = Period::whereRaw('NOW() between startTimeStamp and endTimeStamp')
            ->where('id', $periodId)
            ->get();
        if (count($period) == 0) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.sendNotPossible')]);
        if($period[0]->questionnaire_id != $questionnaire_id)
            return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.paramsNotOk')]);


        $criteria = Criteria::where('questionnaire_id',$questionnaire_id)->get();
        // no criteria assigned!
        if(count($criteria) == 0) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.integrityProblem1')]);

        $kv = [];
        foreach($criteria as $f) {
            $kv[$f->id] = array($f->id, $f->title, $f->info);
        }

        $a_keys = [];
        foreach($criteria as $k) array_push($a_keys, $k->id);
        // make combinations
        $comb = [];
        for($i = 0; $i < count($a_keys); $i++)
            for ($j = $i; $j < count($a_keys); $j++) {
                if($i != $j) {
                    $pairs = array($a_keys[$i], $a_keys[$j]);
                    shuffle($pairs);
                    array_push($comb, $pairs);
                }
            }
        shuffle($comb);

        // assemble criteria
        $collection = [];
        foreach($comb as $key=>$value) {
            $entry = collect([]);
            $entry->push($kv[$value[0]]);
            $entry->push($kv[$value[1]]);
            array_push($collection, $entry);
        }

        if($questionnaire->question == "")
            $questionnaire->question = "Treffen Sie bitte Ihre Auswahl";

        $token = new Token();
        $t = time()."_".uniqid();
        $token->token = (strlen($token) > 32) ? substr($t, 0, 32): $t;
        $token->save();

        if($request->has('viewSize') && ($request->viewSize == 'xs' || $request->viewSize == 'sm'))
            return view('formSmall', ['questionnaire' => $questionnaire, 'pairs' => $collection, 'token' => $token->token, 'periodId' => $periodId]);
        return view('form', ['questionnaire' => $questionnaire, 'pairs' => $collection, 'token' => $token->token, 'periodId' => $periodId]);
    }


    private function checkToken(Request $request) {
        $validation_error = false;
        $tokenCreated = "";
        $token = Token::where('token', $request->token)->first();
       if(!$token)
            $validation_error = true;
        else {
            $tokenCreated = $token->created_at;
            $token->delete();
        }
        return array($validation_error, $tokenCreated);
    }

    private function checkCountOfAnsweredQuestion(Request $request, $questionnaire_id) {
        $quest = Questionnaire::with('criterias')->find($questionnaire_id);
        $soll = count($quest->criterias)*(count($quest->criterias)-1)/2;
        $ist = 0;
        foreach($request->all() as $key=>$val) {
            $arr = explode("_", $key);
            if($arr[0] == "radio")
                $ist++;
        }
        if($ist != $soll)
            return false;
        return true;
    }

    private function checkCriteria(Request $request, $questionnaire_id) {
        $quest = Questionnaire::with('criterias')->find($questionnaire_id);
        $soll = [];
        foreach($quest->criterias as $crit)
            array_push($soll, $crit->id);
        asort($soll);

        $ist = [];
        foreach($request->all() as $key=>$val) {
            $arr = explode("_", $key);
            if($arr[0] == "radio") {
                if(!in_array($val, $ist))
                    array_push($ist, intval($val));
            }
        }
        asort($ist);
        $inter = array_intersect($ist, $soll);
        $arraysAreEqual = ($inter == $ist);
        if(!$arraysAreEqual)
            return false;
        return true;
    }



    public function store(Request $request, $questionnaire_id, $period_id)
    {
        // 1. Check Token
        $retArr = $this->checkToken($request);
        if ($retArr[0] == true) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.alreadySaved')]);
        $tokenCreated = $retArr[1];

        // 1a. Check maximum per minute

        $assInInterval = Assessment::whereRaw('updated_at > NOW() - INTERVAL 10 MINUTE')
            ->where('period_id', $period_id)
            ->count();

        if($assInInterval > config('app.questsThresholdPerMinute')) {
            // deactivate period
            $period = Period::find($period_id);
            $period->length = 0;
            $period->endTimeStamp = $period->startTimeStamp;
            $period->errorCode = 1;
            $period->save();
            return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.questDeactivated')]);
        }
        // 2. check if period is still open and period_id->questionnaire_id == questionnaire_id - Parameter manipulations
        $period = Period::whereRaw('NOW() between startTimeStamp and endTimeStamp')
            ->where('id', $period_id)
            ->get();
        if (count($period) == 0) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.sendNotPossible')]);
        if ($period->first()->questionnaire_id != $questionnaire_id) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.wrongParameters')]);

        // 3. count of answered questions
        if (!$this->checkCountOfAnsweredQuestion($request, $questionnaire_id)) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.countOfAnswersIncorrect')]);

        // 4. get criteria, check the count and correct indices
        if (!$this->checkCriteria($request, $questionnaire_id)) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.paramsNotOk')]);

        // 5. check if questionnaire is active
        $quest = Questionnaire::find($questionnaire_id);
        if ($quest->inactive) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.questInvalid')]);

        // ---------------   all checks OK! ---------------------------------------

        // collect the answers
        $results = []; //contains arrays (points, selection0, selection1, points*sel0, points*sel1)
        // generate array and set "Daempfung" to the config value
        $criteria = Criteria::where('questionnaire_id', $questionnaire_id)->orderBy('sequence')->get();
        foreach ($criteria as $crit)
            $results[$crit->id] = array(config('app.daempfung'));
        $cntQuestions = count($criteria)*(count($criteria)-1)/2;
        for ($i = 0; $i <= $cntQuestions; $i++) {
            $val = 0;
            if ($request->has("radio_" . $i)) {
                $val = $request->{"radio_" . $i};
                $results[$val][0]++;
                //echo "Key: radio_".$i." - ".$results[$val][0]."<br>";
            }
        }

        $selections = Selection::where('questionnaire_id', $questionnaire_id)->orderby('sequence')->get();
        if (count($selections) != 2) return view('errorPage', ['msg' => Lang::get('c4pe.error.formController.integrityProblem2')]);

        $selection0_id = $selections[0]->id;
        $selection1_id = $selections[1]->id;

        $crits = Criteria::with('selections')->orderby('sequence')->where('questionnaire_id', $questionnaire_id)->get();

        $n_rows = [];
        foreach ($crits as $crit) {
            $entry0 = [];
            $entry1 = [];
            foreach ($crit->selections as $sel) {
                //echo $sel->pivot->criteria_id . "/" . $sel->pivot->selection_id . "-" . $sel->pivot->weight . "<br>";
                //echo "Crit_Value:  ".$results[$crit->id][0]."<br>";
                if ($sel->pivot->selection_id == $selection0_id) {
                    //echo "--------".$sel->pivot->selection_id."<br>";
                    $points = $sel->pivot->weight * $results[$crit->id][0];
                    //echo ">>> Points: ".$points."<br>";
                    $entry0[$selection0_id] = array('weight' => $sel->pivot->weight, 'points' => $points);
                }
                if ($sel->pivot->selection_id == $selection1_id) {
                    //echo "--------".$sel->pivot->selection_id."<br>";
                    $points = $sel->pivot->weight * $results[$crit->id][0];
                    //echo ">>> Points: ".$points."<br>";
                    $entry1[$selection1_id] = array('weight' => $sel->pivot->weight, 'points' => $points);
                }
            }
            $n_rows[$crit->id] = array($results[$crit->id][0], $entry0[$selection0_id]['weight'], $entry0[$selection0_id]['points'], $entry1[$selection1_id]['weight'], $entry1[$selection1_id]['points']);
        }

        // calculate sum
        $sel1Sum = 0;
        $sel2Sum = 0;
        foreach ($n_rows as $key => $value) {
            $sel1Sum += intval($value[2]);
            $sel2Sum += intval($value[4]);
        }

        // calculate tendency (-2..strong sel1, -1..weak sel1, 0, 1.. weak sel2, 2..strong sel2)
        $sum = $sel1Sum + $sel2Sum;
        $diff1 = intval($sum * config('app.percentWeak') / 100);
        $diff2 = intval($sum * config('app.percentStrong') / 100);
        $tendency = 0;
        if (abs($sel1Sum - $sel2Sum) > $diff1) {
            if ($sel1Sum > $sel2Sum)
                $tendency = -1;
            else
                $tendency = 1;
        }
        if (abs($sel1Sum - $sel2Sum) > $diff2) {
            if ($sel1Sum > $sel2Sum)
                $tendency = -2;
            else
                $tendency = 2;
        }

        $resArray = array($sel1Sum, $sel2Sum, $tendency);
        // write results
        $ass = new Assessment();
        $ass->questionnaire_id = $questionnaire_id;
        $ass->period_id = $period_id;
        $ass->started = $tokenCreated;
        $ass->selections = json_encode($n_rows);
        $ass->results = json_encode($resArray);
        $ass->save();

//Ziel: Array key=Kriterium -> {score, weight, points, weight, points)
        $user = User::find($quest->user_id);
        $orgaId = $user->organisation_id;

        return view('formResult', ['questionnaire' => $quest, 'selections' => $selections, 'sel1Sum' => $sel1Sum, 'sel2Sum' => $sel2Sum, 'tendency' => $tendency, 'orgaId' => $orgaId]);
    }
}
