<?php

namespace App;

use App\Support\MongoModel;

class Category extends MongoModel
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'categories';

     public $timestamps = false;

      public function application()
    {
      return $this->hasMany('App\Application', 'cat_id');
    }

     public function course()
    {
      return $this->hasMany('App\Course', 'cat_id');
    }

}
