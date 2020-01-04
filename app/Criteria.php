<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criteria extends Model
{

    use SoftDeletes;
    //
    public function selections()
    {
        return $this->belongsToMany('App\Selection')->withPivot("weight");
    }
}
