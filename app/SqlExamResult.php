<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlExamResult extends Model
{
    protected $fillable = [
        'nilai',
        'status',
        'user_id'
    ];
}
