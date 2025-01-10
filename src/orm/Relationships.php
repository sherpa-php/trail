<?php

namespace Sherpa\Trail\orm;

/**
 * ORM Relationships internal trait.
 * <p>
 *     To implement to Sherpa/Core Model class as add-on
 *     for adding relationships definition.
 * </p>
 */
trait Relationships
{
    public static function makeBelongsTo(
        ?int $fk,
        string $target): ORMRelationshipQuery
    {
        return $target::query()
                      ->where("id", $fk)
                      ->toRelationshipQuery(Relationship::BELONGS_TO);
    }
}