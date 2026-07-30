<?php

namespace App;

use App\Support\MongoModel;

class Meeting extends MongoModel
{
    //
    protected $table = 'meetings';

    /**
     * statistics with this meeting
     */
    public function statistics(){
        return $this->hasMany('App\Statistic','meeting_id');
    }

}
