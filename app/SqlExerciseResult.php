<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlExerciseResult extends Model
{
    protected $fillable = [
        'nilai',
        'status',
        'user_id'
    ];
}
