<?php

namespace App\Support;

use MongoDB\Operation\FindOneAndUpdate;

/**
 * Integer primary keys with auto-increment, on top of MongoDB.
 *
 * The old MySQL schema used integer AUTO_INCREMENT primary keys and the
 * application leans on them: foreign keys are stored as integers
 * (persid, appl_id, year_id, ...), routes pass ids as integers, and
 * several queries order by id to find the newest row.
 *
 * MongoDB defaults to ObjectId keys and has no auto-increment, so both
 * behaviours are reproduced here. Migrated documents keep the exact
 * integer _id they had in MySQL, and new documents draw the next value
 * from a `counters` collection, which is what keeps foreign keys
 * written after the migration pointing at the right documents.
 *
 * Used by MongoModel for the ordinary models, and directly by User,
 * which has to extend the package's Authenticatable instead.
 */
trait HasIntegerKey
{
    /**
     * Columns this model stores as integers, from the MySQL schema.
     *
     * Used by MongoBuilder to cast numeric strings — route parameters
     * and request input — before they are compared, because MongoDB
     * will not match '5' against 5. The primary key is always included.
     *
     * @var list<string>
     */
    protected $integerColumns = [];

    /**
     * @return list<string>
     */
    public function getIntegerColumns(): array
    {
        return array_values(array_unique(
            array_merge([$this->getKeyName(), '_id'], $this->integerColumns),
        ));
    }

    /** @inheritdoc */
    public function newEloquentBuilder($query)
    {
        return new MongoBuilder($query);
    }

    public static function bootHasIntegerKey(): void
    {
        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), $model->nextSequenceValue());
            }
        });
    }

    /**
     * Initialise the trait on each instance.
     *
     * Set as initialisers rather than plain properties so classes using
     * the trait do not have to restate them.
     */
    public function initializeHasIntegerKey(): void
    {
        $this->keyType = 'int';
        $this->incrementing = false;
    }

    /**
     * Atomically reserve the next id for this model's collection.
     *
     * findOneAndUpdate with $inc is a single atomic document update, so
     * two concurrent inserts cannot be handed the same id — which a
     * read-max-then-add-one approach would allow.
     */
    public function nextSequenceValue(): int
    {
        $counter = $this->getConnection()
            ->getCollection('counters')
            ->findOneAndUpdate(
                ['_id' => $this->getTable()],
                ['$inc' => ['seq' => 1]],
                ['upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER],
            );

        return (int) $counter['seq'];
    }
}
