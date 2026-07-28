<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
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
