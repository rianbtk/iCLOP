<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $table='tasks';

    public $primaryKey='id';

    public function topic() {
      return $this->belongsTo(App\Topic::class);
    }

    public function getTopic($id) {
      return \App\Topic::find($id)->name;
    }

    public function getListTopic() {
      return \App\Topic::pluck('name', 'id');
    }
}
