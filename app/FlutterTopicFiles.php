<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlutterTopicFiles extends Model
{
    //
    protected $table='topic_files';
    public $primaryKey='id';
    public function topic() {
      return $this->belongsTo(App\FlutterTopic::class);
    }
}
