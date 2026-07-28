<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Areaauth extends Model
{   
  public $timestamps = false;

    public function area()
    {
      return $this->belongsTo('App\Area', 'areaid');
    }
     public function district()
    {
      return $this->belongsTo('App\District', 'districtid');
    }

}

