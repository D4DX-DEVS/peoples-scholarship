<?php

namespace App;

use App\Support\MongoModel;

class Statistic extends MongoModel
{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['appl_id', 'meeting_id'];

	/**
	 * meeting associated with this status change
	 */
	public function meeting(){
		return $this->belongsTo('App\Meeting','meeting_id');
	}

	/**
	 * file associated with this status update
	 */
	public function application(){
		return $this->belongsTo('App\Application','appl_id');
	}
	
    public $timestamps = false;

}
