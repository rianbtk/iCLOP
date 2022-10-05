<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NodejsTopic extends Model
{
    //
    protected $table='topics';
    public function tasks() {
      return $this->hasMany('App\NodejsTask');
    }

    public function topic_files() {
      return $this->hasMany('App\NodejsTopicFiles');
    }

    public function test_files() {
      return $this->hasMany('App\NodejsTestFiles');
    }
}
