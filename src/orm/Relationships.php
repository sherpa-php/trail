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
    public static function belongsTo(string $reference,
                                     string $target,
                                     ?string $column = null): ORMQuery
    {
        return $reference::use();
    }
}