<?php

namespace App\Support;

use MongoDB\Laravel\Eloquent\Model as BaseModel;

/**
 * Base model for the collections migrated across from MySQL.
 *
 * Exists so the thirteen ordinary models pick up integer primary keys
 * and auto-increment without each restating it. User cannot extend this
 * — it has to extend the package's Authenticatable — so it uses
 * HasIntegerKey directly instead.
 */
abstract class MongoModel extends BaseModel
{
    use HasIntegerKey;
}
