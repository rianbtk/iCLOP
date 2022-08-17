<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseFiles extends Model
{
  //
  protected $table = 'exercise_files';
  public $primaryKey = 'id';
  public function topic()
  {
    return $this->belongsTo(App\ExerciseTopic::class);
  }
}

