<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlExamUser extends Model
{
    protected $fillable = [
        'sql_exam_result_id',
        'sql_exam_id',
        'answer',
    ];
}
