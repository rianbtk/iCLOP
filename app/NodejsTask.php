<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NodejsTask extends Model
{
    //
    protected $table='tasks';

    public $primaryKey='id';

    public function topic() {
      return $this->belongsTo(App\NodejsTopic::class);
    }

    public function getTopic($id) {
      return \App\NodejsTopic::find($id)->name;
    }

    public function getListTopic() {
      return \App\NodejsTopic::pluck('name', 'id');
    }
}
