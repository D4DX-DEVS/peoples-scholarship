<?php

namespace App;

use App\Support\MongoModel;

class Course extends MongoModel

{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['cat_id', 'course_enabled'];

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
