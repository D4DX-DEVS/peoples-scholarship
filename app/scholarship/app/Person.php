<?php

namespace App;

use App\Support\MongoModel;
use Illuminate\Support\Facades\Storage;

class Person extends MongoModel
{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['age', 'area', 'district', 'unit'];

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

     /**
      * Public CDN URL of the uploaded photo, or null if none uploaded.
      *
      * Photos for applications that arrived from the People-ERP portal are
      * already public URLs on that system's own Spaces bucket, held in
      * photo_external_url. They are linked rather than copied, so this
      * returns them as-is instead of resolving a filename against the
      * local `spaces` disk.
      *
      * Named photoCdnUrl rather than photoUrl: PHP method names are
      * case-insensitive, and Eloquent's accessor lookup for the "photourl"
      * column would resolve to a same-named getPhotoUrlAttribute(), turning
      * every read of $person->photourl into infinite self-recursion.
      *
      * Built manually rather than via Storage::url() because the Spaces
      * folder prefix contains a space, which the disk's URL generator does
      * not percent-encode — left raw it breaks unquoted CSS url(...) and
      * any non-browser HTTP client.
      */
     public function getPhotoCdnUrlAttribute(): ?string
     {
         if (! empty($this->photo_external_url)) {
             return $this->photo_external_url;
         }

         if (empty($this->photourl)) {
             return null;
         }

         $parts = parse_url(Storage::disk('spaces')->url("uploads/{$this->photourl}"));
         $encodedPath = implode('/', array_map('rawurlencode', explode('/', ltrim($parts['path'], '/'))));

         return $parts['scheme'].'://'.$parts['host'].'/'.$encodedPath;
     }
}
