<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
	public $timestamps = false;
	/**
	 * file the installment belongs to.
	 */
	public function application(){
		return $this->belongsTo('App\Application','appl_id');
	}

	public function getStatus($status)
	{
		switch($status)
		{
			case '1':
				$statusText = "pending";
				$bgColour = "aqua";
				break;
			case '2':
				$statusText = "current";
				$bgColour = "blue";
				break;
			case '3':
				$statusText = "in accounts";
				$bgColour = "blue";
				break;
			case '4':
				$statusText = "delivered";
				$bgColour = "green";
				break;
			case '5':
				$statusText = 'cancelled';
				$bgColour = 'red';
				break;
			default:
				$statusText ='status undefined';
				$bgColour = 'red';
		}
		return ['statusText'=>$statusText,'bgColour'=>$bgColour];
	}
}
 