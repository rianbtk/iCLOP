<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AndroidTask extends Model
{
    //
    protected $table='tasks';

    public $primaryKey='id';

    public function topic() {
      return $this->belongsTo(App\AndroidTopic::class);
    }

    public function getTopic($id) {
      return \App\AndroidTopic::find($id)->name;
    }

    public function getListTopic() {
      return \App\AndroidTopic::pluck('name', 'id');
    }
}
