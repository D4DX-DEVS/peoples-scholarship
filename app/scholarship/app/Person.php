<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'persons';
     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
     public $timestamps = false;


     /**
     * Get the user that owns the phone.
     */
     public function application()
     {
         return $this->hasMany('App\Application', 'persid');
     }
}
