<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model

{
    /**
 * The table associated with the model.
 *
 * @var string
 */
    protected $table = 'courses';
    public $timestamps = false;

    public function category()
    {
    return $this->belongsTo('App\Category', 'cat_id');
    }


}
