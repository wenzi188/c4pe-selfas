<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Organisation;
use App\User;
use App\Questionnaire;
use App\Period;
use Lang;
use Carbon\Carbon;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $organisations = Organisation::where('inactive', false)->get();

        return view('home', ['organisations' => $organisations]);
    }

    public function formList(Request $request, $organisation_id)
    {
        $organisation = Organisation::where('inactive', false)
            ->where('id', $organisation_id)
            ->first();
        if (!$organisation)
            return view('errorPage', ['msg' => Lang::get('c4pe.error.homeController.parametersNotAllowed')]);

        $myfriends = User::where('organisation_id', $organisation_id)->get();
        $myfriendsArray = [];
        foreach ($myfriends as $friend)
            array_push($myfriendsArray, $friend->id);

        $quests = Questionnaire::with('activePeriods')->whereIn('user_id', $myfriendsArray)
            ->where('inactive', false)
            ->orderBy('title', 'asc')
            ->get();

        $openQuests = 0;
        foreach ($quests as $quest)
            $openQuests += count($quest->activePeriods);

        return view('formList', ['organisation' => $organisation, 'quests' => $quests, 'openQuests' => $openQuests]);
    }
    public function impressum(Request $request) {
        return view('impressum', []);
    }


    public function acceptDSGVO(Request $request, User $user)
    {
        return view('acceptDSGVO', ['user' => $user]);
    }

    public function acceptedDSGVO(Request $request)
    {
        $user = User::where('id',$request->solspec)
                    ->where('acceptedDSGVO', null)
                    ->get()
                    ->first();
        if($user) {
            $user->acceptedDSGVO = Carbon::now();
            $user->save();
        }

        return redirect(route("login"));
    }

}
