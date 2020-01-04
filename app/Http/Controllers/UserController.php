<?php

namespace App\Http\Controllers;

use \App\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;
use App\Organisation;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('listUsers', User::class);
        if(Auth::user()->isSuperAdmin)
            $users = \App\User::orderBy('lastname','asc')
                ->orderBy('firstname','asc')
                ->get();
        else
            $users = \App\User::orderBy('lastname','asc')
                ->with('myOrganisation:id,title')
                ->where('organisation_id', Auth::user()->organisation_id)
                ->where('role', '!=', '9')
                ->orderBy('firstname','asc')
                ->get();
        $userNew = new User();
        $userNew->id = -1;

        return view('users.users')->with(['users' => $users, 'userNew' => $userNew]);
    }

//    public function edit(User $user)
    public function edit($user_id)
    {
        if($user_id == -1) {
            $user = new User();
            $user->id = -1;
            $user->organisation_id = Auth::user()->organisation_id;
            $user->role = 0;
        }
        else
            $user = User::find($user_id);

        $this->authorize('manageUser', $user);

        $orgas = Organisation::all();
        return view('users.userEdit')->with(['user' => $user, 'orgas' => $orgas]);
    }

//    public function update(Request $request, User $user)
    public function update(Request $request, $user_id)
    {
        if($user_id == -1) {
            $user = new User();
            $user->id = -1;
            if(Auth::user()->isOrgaAdmin)
                $user->organisation_id = Auth::user()->organisation_id;
        }
        else
            $user = User::find($user_id);

        $this->authorize('updateUser', $user);

        if(Auth::user()->isSuperAdmin)
            $request->validate([
                'firstname' => 'required|max:32|min:2',
                'lastname' => 'required|max:32|min:2',
                'email' => 'required|max:64|email|unique:users,email,'.$user->id,
                'role' => 'integer|min:0|max:1',
                'organisation' => 'required|exists:organisations,id',
                'passwordNew' => 'sometimes|max:32|min:6',
            ]);

        if(Auth::user()->isOrgaAdmin)
            $request->validate([
                'firstname' => 'required|max:32|min:2',
                'lastname' => 'required|max:32|min:2',
                'email' => 'required|max:64|email|unique:users,email,'.$user->id,
                'role' => 'integer|min:0|max:1',
                'passwordNew' => 'sometimes|max:32|min:6',
            ]);

        if(!Auth::user()->isSuperAdmin && !Auth::user()->isOrgaAdmin)
            $request->validate([
                'firstname' => 'required|max:32|min:2',
                'lastname' => 'required|max:32|min:2',
                'email' => 'required|max:64|email|unique:users,email,'.$user->id,
            ]);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->name = $request->lastname." ".$request->firstname;
        $user->email = $request->email;
        if($user->id == -1) {
            $user->password = bcrypt($request->input('passwordNew'));
            $user->id = null;
        }
        if(Auth::user()->isSuperAdmin || Auth::user()->isOrgaAdmin)
            $user->role = $request->role;

        if(Auth::user()->isSuperAdmin)
            $user->organisation_id = $request->organisation;

        $user->save();

        return redirect()->route('questionnaire.list', $user);
    }

    public function updatePassword(Request $request, User $user)
    {
        $this->authorize('updateUser', $user);

        $request->validate([
            'password' => 'required|max:32|min:6|same:password2',
            'password2' => 'required|max:32|min:6|same:password',
        ]);

        $user->password = bcrypt($request->input('password'));
        $user->update();
        return redirect()->route('user.edit', $user);
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('deleteUser', $user);
        $user->name = 'Anonym';
        $user->firstname = 'Anonym';
        $user->lastname = 'Anonym';
        $user->password = bcrypt('An_'.uniqid());
        $user->email = 'An_'.uniqid();
        $user->save();
        $user->delete();
        return redirect()->route('user.index', $user);
    }



}
