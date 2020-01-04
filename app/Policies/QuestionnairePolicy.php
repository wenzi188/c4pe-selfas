<?php

namespace App\Policies;

use App\Questionnaire;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionnairePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function manageQuestionnaire(User $user, Questionnaire $quest)
    {
        if($user->id == $quest->user_id)
            return true;
        $qUser = User::find($quest->user_id);
        if($qUser->organisation_id == $user->organisation_id && $user->IsOrgaAdmin)
            return true;
        if($user->IsSuperAdmin)
            return true;
        return false;
    }


}
