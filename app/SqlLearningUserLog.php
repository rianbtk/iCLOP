<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlLearningUserLog extends Model
{
    protected $fillable = [
        'syntax',
        'rollback',
        'result',
        'status',
        'sql_learning_user_id',
    ];
}
