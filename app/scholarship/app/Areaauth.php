<?php

namespace App;

use App\Support\MongoModel;

class Areaauth extends MongoModel
{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['areaid', 'districtid'];
   
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

