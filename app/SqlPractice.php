<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlPractice extends Model
{
    protected $fillable = [
        'name',
        'question'
    ];

    public function question()
    {
        return $this->hasMany(SqlPracticeQuestion::class);
    }

    public function questions()
    {
        return $this->hasMany(SqlPracticeQuestion::class);
    }
}
