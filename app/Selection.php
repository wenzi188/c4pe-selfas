<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Selection extends Model
{
    use SoftDeletes;
    //
    public function criterias()
    {
        return $this->belongsToMany('App\Criteria');
    }

}
