<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    protected $table='class_members';

    public function classid() {
        return $this->belongsTo(App\Classroom::class);
    }

    public function student() {
        return $this->belongsTo(App\User::class);
    }
}
