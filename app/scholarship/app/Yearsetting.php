<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yearsetting extends Model
{
    public $timestamps = false;

    public function applications()
    {
      return $this->hasMany('App\Application', 'year_id');
    }

    public function getApplicationsCount(){
      return $this->applications()->count();
    }
}
