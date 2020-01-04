<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Questionnaire extends Model
{
    use SoftDeletes;
    //
    public function criterias()
    {
        return $this->hasMany('App\Criteria');
    }

    public function criteriasIds() {
        return $this->hasMany('App\Criteria')->select(['id']);
    }

    public function periods()
    {
        return $this->hasMany('App\Period');
    }


    public function activePeriods()
    {
        return $this->hasMany('App\Period') ->whereRaw('NOW() between startTimeStamp and endTimeStamp');
    }

    public function selections()
    {
        return $this->hasMany('App\Selection')->orderBy('sequence');
    }

    public function creator() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function creatorOfMyOrganisation() {
        return $this->belongsTo('App\User', 'user_id')->where('organisation_id','=', 11);
    }



}
