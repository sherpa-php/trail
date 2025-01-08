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
        int $fk,
        string $target): ORMQuery
    {
        return $target::use()
                      ->where("id", $fk);
    }
}