<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnityTopic extends Model
{
    //
    protected $table='topics';
    public function tasks() {
      return $this->hasMany('App\UnityTask');
    }

    public function topic_files() {
      return $this->hasMany('App\UnityTopicFiles');
    }

    public function test_files() {
      return $this->hasMany('App\UnityTestFiles');
    }
}
