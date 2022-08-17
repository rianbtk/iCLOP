<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'roleid', 'email', 'password','uplink','status'
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

    public function checkRoleId($roleid) {
      if ($roleid=='student') {
        return Redirect::to('student/home');
      } elseif ($roleid=='teacher') {
        return Redirect::to('teacher/home');
      } elseif ($roleid=='admin') {
        return Redirect::to('admin/admin');
      } else {
        return Redirect::to('/home');
      }
    }
}
