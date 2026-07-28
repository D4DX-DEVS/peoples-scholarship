<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{

    protected $table = 'statuses';
    public $timestamps = false;


    public function application()
    {
        return $this->hasMany('App\Application', 'status');
    }

}
