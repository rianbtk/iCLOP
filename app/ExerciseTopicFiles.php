<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseTopicFiles extends Model
{
  //
  protected $table = 'exercise_topic_files';
  public $primaryKey = 'id';
  public function topic()
  {
    return $this->belongsTo(App\ExerciseTopic::class);
  }
}

