<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseTopic extends Model
{
  //
  protected $table = 'exercises';
  public function tasks()
  {
    return $this->hasMany('App\Task');
  }

  public function topic_files()
  {
    return $this->hasMany('App\TopicFiles');
  }

  public function test_files()
  {
    return $this->hasMany('App\TestFiles');
  }
}

