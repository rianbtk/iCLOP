<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlutterTask extends Model
{
    //
    protected $table='tasks';

    public $primaryKey='id';

    public function topic() {
      return $this->belongsTo(App\FlutterTopic::class);
    }

    public function getTopic($id) {
      return \App\FlutterTopic::find($id)->name;
    }

    public function getListTopic() {
      return \App\FlutterTopic::pluck('name', 'id');
    }
}
