<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlPracticeUser extends Model
{
    protected $fillable = [
        'syntax',
        'result',
        'poin',
        'correct',
        'wrong',
        'sql_practice_question_id',
        'user_id'
    ];
}
