<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AndroidTopic extends Model
{
    //
    protected $table='topics';
    public function tasks() {
      return $this->hasMany('App\AndroidTask');
    }

    public function topic_files() {
      return $this->hasMany('App\AndroidTopicFiles');
    }

    public function test_files() {
      return $this->hasMany('App\AndroidTestFiles');
    }
}
