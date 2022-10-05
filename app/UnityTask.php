<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnityTask extends Model
{
    //
    protected $table='tasks';

    public $primaryKey='id';

    public function topic() {
      return $this->belongsTo(App\UnityTopic::class);
    }

    public function getTopic($id) {
      return \App\UnityTopic::find($id)->name;
    }

    public function getListTopic() {
      return \App\UnityTopic::pluck('name', 'id');
    }
}
