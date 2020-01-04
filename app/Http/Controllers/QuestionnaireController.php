<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use App\Questionnaire;
use App\Criteria;
use App\Selection;
use App\Period;
use App\User;
use App\Assessment;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\App;
use Lang;

class QuestionnaireController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function downloadCSV(Request $request, $quest_id, $period_id)
    {
        $csv_data = [];
        $header = array("pId", "pTitle", "pStartTimeStamp", "pEndTimeStamp", "aStarted", "aUpdated_at",
            "aSelection1Title", "aSelection1Sum", "aSelection2Title", "aSelection2Sum", "aTendency");
        $questionnaire = Questionnaire::with('selections')->find($quest_id);
        $this->authorize('manageQuestionnaire', $questionnaire);

        $crits = $questionnaire->criterias;
        $ind = 1;
        foreach($crits as $crit) {
            array_push($header, "Criteria".$ind."Title");
            array_push($header, "Criteria".$ind."Sum");
            array_push($header, "Criteria".$ind."WeightSel1");
            array_push($header, "Criteria".$ind."PointsSel1");
            array_push($header, "Criteria".$ind."WeightSel2");
            array_push($header, "Criteria".$ind."PointsSel2");
            $ind++;
        }
        $critTitles = [];
        foreach($crits as $crit) {
            $critTitles[$crit->id] = $crit->title;
        }


        array_push($csv_data,$header);

        if($period_id != -1)
            $assessments = Assessment::where('questionnaire_id', $quest_id)
                ->where('period_id', $period_id)
                ->orderBy('started')
                ->get();
        else {
            $assessments = Assessment::where('questionnaire_id', $quest_id)
                ->orderBy('period_id')
                ->orderBy('started')
                ->get();

        }

        $pId = -1;
        foreach($assessments as $ass) {
            $row = [];
            array_push($row, $ass->period_id);
            if($ass->period_id != $pId) {
                $period = Period::find($ass->period_id);
                $pId = $period->id;
            }
            array_push($row, $period->title);
            array_push($row, $period->startTimeStamp);
            array_push($row, $period->endTimeStamp);
            array_push($row, $ass->started);
            array_push($row, $ass->updated_at);
            $res = json_decode($ass->results);
            array_push($row, $questionnaire->selections[0]->title);
            array_push($row, $res[0]);
            array_push($row, $questionnaire->selections[1]->title);
            array_push($row, $res[1]);
            array_push($row, $res[2]);
            $res = json_decode($ass->selections);
            foreach($res as $key=> $val) {
                array_push($row, $critTitles[$key]);
                array_push($row, $val[0]);
                array_push($row, $val[1]);
                array_push($row, $val[2]);
                array_push($row, $val[3]);
                array_push($row, $val[4]);
            }
            array_push($csv_data, $row);
        }

        return new StreamedResponse(
            function () use ($csv_data) {
                // A resource pointer to the output stream for writing the CSV to
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                foreach ($csv_data as $row) {
                    // Loop through the data and write each entry as a new row in the csv
                    fputcsv($handle, $row, ";");
                }

                fclose($handle);
            },
            200,
            [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename=questionnaire.csv'
            ]
        );

    }

    public function statistics(Request $request, $quest_id, $period_id) {

        $questionnaire = Questionnaire::with('selections')->find($quest_id);
        $this->authorize('manageQuestionnaire', $questionnaire);

        $sel1Title = $questionnaire->selections[0]->title;
        $sel2Title = $questionnaire->selections[1]->title;

        if($period_id == -1)
            $periods = Period::where('questionnaire_id', $quest_id)->get();
        else {
            $periods = Period::where('questionnaire_id', $quest_id)
                ->where('id', $period_id)
                ->get();
            if(count($periods) == 0)
                return view('errorPage', ['msg'=>Lang::get('c4pe.error.questController.inputNotAllowed')]);
        }
        if($period_id != -1)
            $assessments = Assessment::where ('questionnaire_id', $quest_id)
                ->where('period_id', $period_id)
                ->orderBy('started')
                ->get();
        else
            $assessments = Assessment::where ('questionnaire_id', $quest_id)
                ->orderBy('period_id')
                ->orderBy('started')
                ->get();

        // calculate the results
        $sel1Strong = 0; $sel2Strong=0; $sel1Weak = 0; $sel2Weak = 0; $selNeutral = 0;
        foreach($assessments as $ass) {
            $arr = json_decode($ass->results);
            if($arr[2] == 0) $selNeutral++;
            if($arr[2] == -1) $sel1Weak++;
            if($arr[2] == -2) $sel1Strong++;
            if($arr[2] == 1) $sel2Weak++;
            if($arr[2] == 2) $sel2Strong++;
        }

        // data for graphic
        $sumPerCrit = [];
        $sumPerCrit = [];
        $crits = $questionnaire->criteriasIds; //()->selections->get();
        foreach($crits as $crit)
            $sumPerCrit[$crit->id] = 0;

        foreach($assessments as $ass) {
            $arr = json_decode($ass->selections);
            foreach($arr as $key => $value)
                $sumPerCrit[$key] += $value[0];
        }
        $sumPerNamedCrit = [];
        $crits = $questionnaire->criterias;
        foreach($sumPerCrit as $key=>$value) {
            foreach($crits as $c)
                if($c->id == $key)
                    $sumPerNamedCrit[$c->title] = $value;
        }
        $objArr = [];
        foreach($sumPerNamedCrit as $key=> $value) {
            $r = [];
            $r["name"] = (strlen($key)>18) ? substr($key, 0, 18)."...": $key ;
            $r["value"] = $value;
            array_push($objArr, (object)$r);
        }

        return view('statistics')->with(['assessments' => $assessments, 'periods' => $periods, 'questionnaire' => $questionnaire,
            'periodId' => $period_id, 'sel1Strong'=>$sel1Strong, 'sel2Strong' => $sel2Strong, 'sel1Weak' => $sel1Weak, 'sel2Weak' => $sel2Weak,
            'selNeutral' => $selNeutral, 'graphicData' => json_encode($objArr)]);

    }

    public function index()
    {
        if(Auth::user()->isOrgaAdmin) {
            $myfriends = User::where('organisation_id', Auth::user()->organisation_id)->get();
            $myfriendsArray = [];
            foreach($myfriends as $friend)
                array_push($myfriendsArray, $friend->id);

            $quests = Questionnaire::whereIn('user_id', $myfriendsArray)
                ->where('inactive', false)
                ->orderBy('title', 'asc')
                ->get();
        }
        if(Auth::user()->isNormalUser) {
            $quests = Questionnaire::with('creator:id,lastname,organisation_id')
                ->where('user_id', Auth::user()->id)
                ->where('inactive', false)
                ->orderBy('title', 'asc')
                ->get();
        }
        if(Auth::user()->isSuperAdmin) {
            $quests = Questionnaire::with('creator:id,lastname,organisation_id')
                ->where('inactive', false)
                ->orderBy('title', 'asc')
                ->get();
        }

        $questIds = [];
        foreach($quests as $quest)
            array_push($questIds, $quest->id);

        $questionnaireWithAssessments = [];
        $assessments = Assessment::whereIn('questionnaire_id', $questIds)
            ->distinct('questionnaire_id')
            ->get();
        foreach($assessments as $ass)
            array_push($questionnaireWithAssessments, $ass->questionnaire_id);

        $periods = Period::whereIn('questionnaire_id', $questIds)
            ->whereRaw('NOW() between startTimeStamp and endTimeStamp')
            ->distinct('questionnaire_id')
            ->get();
        $actQuestionnaireArr = [];
        foreach($periods as $period)
            array_push($actQuestionnaireArr, $period->questionnaire_id);



        $newQuest = new Questionnaire;
        $newQuest->id = -1;

        return view('questionnaireList')->with(['actQuestionnaireArr' => $actQuestionnaireArr,
            'questionnaireWithAssessments' => $questionnaireWithAssessments, 'quests' => $quests, 'newQuest' => $newQuest]);
    }

    public function store(Request $request, $quest_id) {

        $rows = json_decode($request->criteriaJson);
        $sequence = 0;
        $validationError = false;

        $qtitle = (strlen($request->qtitle) > 128 ? substr($request->qtitle, 0, 128): $request->qtitle);
        $question = (strlen($request->question) > 128 ? substr($request->question, 0, 128): $request->question);
        $description = (strlen($request->description) > 512 ? substr($request->description, 0, 512): $request->description);
        $selectionList_0 = (strlen($request->selectionList_0) > 64 ? substr($request->selectionList_0, 0, 64): $request->selectionList_0);
        $selectionList_1 = (strlen($request->selectionList_1) > 64 ? substr($request->selectionList_1, 0, 64): $request->selectionList_1);
        if(strlen($qtitle) == 0 || strlen($selectionList_0) == 0 ||strlen($selectionList_1) == 0)
            $validationError = true;

        $allowChanges = true;
        $questionnaire = new Questionnaire();
        if($quest_id != -1) {
            $questionnaire = Questionnaire::find($quest_id);
            $this->authorize('manageQuestionnaire', $questionnaire);
            $assessments = Assessment::where('questionnaire_id', $quest_id)
                ->distinct('questionnaire_id')
                ->get();
            if (count($assessments) > 0) $allowChanges = false;
        }
        if($allowChanges)
            $questionnaire->title = $qtitle;
        $questionnaire->description = $description;
        $questionnaire->question = $question;

        if($quest_id == -1)
            $questionnaire->user_id = Auth::user()->id;
        if(!$validationError)   $questionnaire->save();

        $selection0 = new Selection();
        if($request->selection_0_id != "-1") {
            $selection0 = selection::find($request->selection_0_id);
            if($selection0->questionnaire_id != $questionnaire->id)
                $validationError = true;
        }
        $selection0->title = $selectionList_0;
        $selection0->sequence = 0;
        $selection0->questionnaire_id = $questionnaire->id;
        if(!$validationError)   $selection0->save();

        $selection1 = new Selection();
        if($request->selection_1_id != -1) {
            $selection1 = selection::find($request->selection_1_id);
            if($selection1->questionnaire_id != $questionnaire->id)
                $validationError = true;
        }
        $selection1->title = $selectionList_1;
        $selection1->sequence = 1;
        $selection1->questionnaire_id = $questionnaire->id;
        if(!$validationError)   $selection1->save();

        if($validationError)
            return '<div class="alert alert-danger"><div>Fehler beim Speichern - ungültige Werte erkannt</div></div>';

        // create list of attached values for criteria_selection for later deletion
        $critList = [];
        $crits = $questionnaire->criteriasIds; //()->selections->get();
        foreach($crits as $crit)
            array_push($critList, $crit->id);

        foreach($rows as $row) {
            $id = $row->id;
            $title = (strlen($row->title) > 64 ? substr($row->title, 0, 64): $row->title);
            $info = (strlen($row->info) > 128 ? substr($row->info, 0, 128): $row->info);
            if(strlen($title) == 0)   // do not save, if title is empty - additional check, should not happen due to browser
                continue;
            $weight_0 = intval($row->weights[0]->weight);
            $selection_0_id = $selection0->id;
            $weight_1 = intval($row->weights[1]->weight);
            $selection_1_id =  $selection1->id;
            if (!in_array($weight_0, config('app.allowedWeights')))
                $weight_0 = config('app.allowedWeights')[0];
            if (!in_array($weight_1, config('app.allowedWeights')))
                $weight_1 = config('app.allowedWeights')[0];

            $criteria = new Criteria();
            if ($id > -1 )
                $criteria = Criteria::find($id);
            $criteria->title = $title;
            $criteria->info = $info;
            $criteria->sequence = $sequence;
            $criteria->questionnaire_id = $questionnaire->id;
            $criteria->save();

            if($allowChanges) {
                $criteria->selections()->detach($selection0->id);
                $criteria->selections()->detach($selection1->id);
                $criteria->selections()->attach($selection0->id, ['weight' => $weight_0]);
                $criteria->selections()->attach($selection1->id, ['weight' => $weight_1]);
            }

            if (($key = array_search($criteria->id, $critList)) !== false)
                unset($critList[$key]);

            $sequence++;
        }
        // remove criteria which are not used anymore
        foreach($critList as $crit) {
            $c = Criteria::find($crit);
            $c->selections()->detach($selection0);
            $c->selections()->detach($selection1);
            $c->delete();
        }
        return redirect()->route('questionnaire.list');
    }

    public function edit(Request $request, $quest_id) {

        $modeCopy = false;
        if($request->has('mode'))
            $modeCopy = true;
        $entries = array();
        if($quest_id == -1) {
            $quest = new Questionnaire();
            $quest->id = -1;
            $allowChanges = true;
        }
        else {
            $quest = Questionnaire::find($quest_id);
            $this->authorize('manageQuestionnaire', $quest);

            $allowChanges = true;
            if(!$modeCopy) {
                $assessments = Assessment::where('questionnaire_id', $quest->id)
                    ->distinct('questionnaire_id')
                    ->get();
                if (count($assessments) > 0) $allowChanges = false;
            }
            $criterias = $quest->criterias()->orderBy("sequence")->get();
            $helperId = -1000;
            foreach($criterias as $criteria) {
                $selections = $criteria->selections()->orderBy("sequence")->get();
                $weights = array();
                foreach($selections as $selection) {
                    $weight = $selection->pivot->weight;
                    array_push($weights, array("id"=>$selection->id, "weight"=>$weight));
                }
                if($modeCopy) {
                    $entry = array("id" => $helperId, "title" => $criteria->title, "info" => $criteria->info, "weights" => $weights);
                    $helperId--;
                }
                else
                    $entry = array("id" => $criteria->id, "title"=>$criteria->title, "info" =>$criteria->info, "weights"=>$weights);
                array_push($entries, $entry);
            }
        }
        $criteriaList = json_encode($entries);
        $selectionList = $quest->selections()->orderBy("sequence")->get();
        return view('questionnaire')->with(['quest' => $quest, 'modeCopy' => $modeCopy,
            'criteriaList' => $criteriaList, 'selectionList' => $selectionList,
            'allowChanges' => $allowChanges]);
    }

    public function periodList(Request $request, $quest_id)
    {
        $quest = Questionnaire::find($quest_id);
        $this->authorize('manageQuestionnaire', $quest);

        $periodIds = Assessment::where('questionnaire_id', $quest_id)
            ->select('period_id')
            ->distinct('period_id')
            ->get();
        $pIds = [];
        foreach($periodIds as $id)
            array_push($pIds, $id->period_id);

        $pNew = new Period();
        $pNew->id = -1;
        $pNew->startTimeStamp = "";
        $pNew->length = 1;
        $pNew-> questionnaire_id = $quest->id;

        $periods = Period::where("questionnaire_id", $quest->id)->orderby("startTimeStamp", "desc")->get();
        $periods->prepend($pNew);

        foreach($periods as $period)
            if($period->startTimeStamp)
                $period->startTimeStamp = $this->reformat($period->startTimeStamp);

        return view('periodList')->with(['quest' => $quest, 'periods'=>$periods, 'pIds' => $pIds]);
    }

    public function periodStore(Request $request, $quest_id, $period_id) {

        $quest = Questionnaire::find($quest_id);
        $this->authorize('manageQuestionnaire', $quest);

        $period = new Period();
        $period->questionnaire_id = $quest_id;
        if ($period_id != -1) {
            $period = Period::find($period_id);
            if($period->questionnaire_id != $quest_id) return view('errorPage', ['msg' => Lang::get('c4pe.error.questController.accessNotAllowed')]);
        }

        $title =(strlen($request->title) > 64 ? substr($request->title, 0, 64): $request->title);
        $length = intval($request->length);
        if($length < 0) $length = 0;
        if($length > 720) $length = 720;

        $year = ""; $month = ""; $day = ""; $hour = ""; $minute = "";
        if($request->start != "") {
            $d = explode(" ", $request->start);
            $date = explode(".", $d[0]);
            if (count($date) == 3) {
                $day = $date[0];
                $month = $date[1];
                $year = $date[2];
            }
            $time = explode(":", $d[1]);
            if (count($time) == 2) {
                $hour = $time[0];
                $minute = $time[1];
            }
        }
        $start = $year."-".$month."-".$day." ".$hour.":".$minute.":00";
        $timeStampOK = $this->validateDate($start);

        // check overlapping periods --> do not save!
        if($timeStampOK) {
            $end = Carbon::create($year, $month, $day, $hour, $minute, 0);
            if ($length == 0 or $length == "")
                $endTimeStamp = $start;
            else
                $endTimeStamp = $end->addHours($length);

            $periods1 = Period::where('questionnaire_id', $quest_id)
                ->where('id', '!=', $period_id)
                ->whereRaw('? between startTimeStamp and endTimeStamp', [$start])
                ->get();
            $periods2 = Period::where('questionnaire_id', $quest_id)
                ->where('id', '!=', $period_id)
                ->whereRaw('? between startTimeStamp and endTimeStamp', [$endTimeStamp])
                ->get();
            $periods3 = Period::where('questionnaire_id', $quest_id)
                ->where('id', '!=', $period_id)
                ->whereRaw('? < startTimeStamp and ? > endTimeStamp', [$start, $endTimeStamp])
                ->get();
            $cnt = count($periods1) + count($periods2) + count($periods3);
            if ($cnt > 0) {
                return view('errorPage', ['msg' => Lang::get('c4pe.error.questController.overlapping')]);
            }
        }

        if(strlen($title) != 0){  // store only if title not is empty
            $period->title = $title;
            $period->length = $length;
            if($request->start == "") {
                $period->startTimeStamp = null;
                $period->endTimeStamp = null;
            }
            if($timeStampOK) {
                $period->startTimeStamp = $start;
                $end = Carbon::create($year, $month, $day, $hour, $minute, 0);
                if($period->length == 0)
                    $period->endTimeStamp = $period->startTimeStamp;
                else
                    $period->endTimeStamp = $end->addHours($period->length);
            }
            $period->errorCode = 0;
            $period->save();
        }

        return redirect()->route('questionnaire.list');
    }


    public function periodDelete(Request $request, $quest_id, $period_id)
    {
        if($period_id != -1) {
            $questy = Questionnaire::find($quest_id);
            $this->authorize('manageQuestionnaire', $questy);

            $ass = Assessment::where('period_id', $period_id)
                ->where('questionnaire_id', $quest_id)
                ->get();
            if(count($ass) > 0) return view('errorPage', ['msg' => Lang::get('c4pe.error.questController.removePeriodNotAllowed')]);

            $period = Period::find($period_id);
            if ($period->questionnaire_id == $quest_id) {
                $period->delete();
            }
        }
        return redirect()->route('questionnaire.list');
    }

    public function remove(Request $request, $quest_id) {
        $questionnaire = Questionnaire::find($quest_id);
        $this->authorize('manageQuestionnaire', $questionnaire);

        $questionnaire->inactive = true;
        $questionnaire->save();

        return redirect()->route('questionnaire.list');
    }



    function validateDate($date, $format = 'Y-m-d H:i:s')    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function reformat($ts) {
        $d = explode(" ", $ts);
        $date = explode("-",$d[0]);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $time = explode(":",$d[1]);
        $hour = $time[0];
        $minute = $time[1];
        return $day.".".$month.".".$year." ".$hour.":".$minute;
    }

}
