<?php

namespace App;

use App\Support\MongoModel;

class Status extends MongoModel
{

    protected $table = 'statuses';
    public $timestamps = false;


    public function application()
    {
        return $this->hasMany('App\Application', 'status');
    }

}
