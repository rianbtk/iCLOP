<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlExercise extends Model
{
    protected $fillable = [
        'question',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
    ];
}
