<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlPracticeQuestion extends Model
{
    protected $fillable = [
        'question',
        'syntax',
        'sql_practice_id'
    ];

    public function practice()
    {
        return $this->belongsTo(SqlPractice::class, 'sql_practice_id', 'id');
    }
}
