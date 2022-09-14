<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlExerciseUser extends Model
{
    protected $fillable = [
        'sql_exercise_result_id',
        'sql_exercise_id',
        'answer',
    ];
}
