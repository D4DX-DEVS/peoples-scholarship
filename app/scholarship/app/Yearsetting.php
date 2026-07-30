<?php

namespace App;

use App\Support\MongoModel;

class Yearsetting extends MongoModel
{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['applimit', 'entryenable'];

    public $timestamps = false;

    public function applications()
    {
      return $this->hasMany('App\Application', 'year_id');
    }

    public function getApplicationsCount(){
      return $this->applications()->count();
    }
}
