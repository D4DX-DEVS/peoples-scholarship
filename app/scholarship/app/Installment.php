<?php

namespace App;

use App\Support\MongoModel;

class Installment extends MongoModel
{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['appl_id', 'installment_number', 'status'];

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
 