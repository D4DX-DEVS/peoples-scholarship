<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
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
