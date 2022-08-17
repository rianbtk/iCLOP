<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DownloadHistory extends Model
{
    //
	    protected $table='download_history';

    public function userid() {
        return $this->belongsTo(App\User::class);
      }
}
