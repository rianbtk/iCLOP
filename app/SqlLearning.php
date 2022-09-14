<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlLearning extends Model
{
    protected $fillable = [
        'name',
        'syntax',
        'file'
    ];
}
