<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqlLearningUser extends Model
{
    protected $fillable = [
        'status',
        'sql_learning_id',
        'user_id',
    ];

    public function input()
    {
        return $this->hasMany(SqlLearningUserLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
