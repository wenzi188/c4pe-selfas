<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Organisation;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getIsOrgaAdminAttribute()
    {
        if($this->role == 1)
            return true;
        return false;
    }

    public function getIsSuperAdminAttribute()
    {
        if($this->role == 9)
            return true;
        return false;
    }

    public function getIsNormalUserAttribute()
    {
        if($this->role == 0)
            return true;
        return false;
    }

    public static function getRoles()  {
        return array(0, 1);
    }

    public function myOrganisation() {
        return $this->belongsTo('App\Organisation', 'organisation_id');
    }


}
