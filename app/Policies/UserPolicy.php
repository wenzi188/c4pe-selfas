<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function listUsers(User $user)
    {
        return $user->IsOrgaAdmin || $user->IsSuperAdmin ;
    }

    public function manageUser(User $user, User $userRec)
    {
        return $userRec->id === $user->id || $user->IsOrgaAdmin || $user->IsSuperAdmin;
    }

    public function updateUser(User $user, User $userRec)
    {
        if($user->id == $userRec->id)   return true;
        if($user->isSuperAdmin)     return true;
        if($user->isOrgaAdmin && $user->id != $userRec->id && $user->organisation_id == $userRec->organisation_id)
            return true;

        return false;
    }

    public function changeUserRole(User $user)
    {
        return $user->IsSuperAdmin || $user->IsOrgaAdmin;
    }

    public function changeUserOrganisation(User $user) {
        return $user->IsSuperAdmin;
    }

    public function deleteUser(User $user, User $userRec)
    {
        if($user->id == $userRec->id) return false;

        if($user->IsSuperAdmin) return true;

        if ($user->IsOrgaAdmin && $userRec->organisation_id == $user->organisation_id) return true;

        return false;
    }



}
