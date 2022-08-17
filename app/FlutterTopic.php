<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlutterTopic extends Model
{
    //
    protected $table='topics';
    public function tasks() {
      return $this->hasMany('App\FlutterTask');
    }

    public function topic_files() {
      return $this->hasMany('App\FlutterTopicFiles');
    }

    public function test_files() {
      return $this->hasMany('App\FlutterTestFiles');
    }
}
