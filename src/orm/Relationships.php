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

    public static function makeHasMany(
        ?int $fk,
        string $fkName,
        string $target): ORMRelationshipQuery
    {
        return $target::query()
                      ->where($fkName, $fk)
                      ->toRelationshipQuery(Relationship::HAS_MANY);
    }

    public static function makeHasOne(
        ?int $fk,
        string $fkName,
        string $target): ORMRelationshipQuery
    {
        return $target::query()
                      ->where($fkName, $fk)
                      ->toRelationshipQuery(Relationship::HAS_ONE);
    }

    public static function makeManyToMany(
        ?int $leftFk,
        string $leftFkName,
        string $rightFkName,
        string $pivotTable,
        string $target): ORMRelationshipQuery
    {
        $pivot = $pivotTable::query()
                            ->where($leftFkName, $leftFk)
                            ->get();

        $query = $target::query();

        foreach ($pivot as $row)
        {
            $query->where("id", $row->data->$rightFkName);
        }

        return $query->toRelationshipQuery(Relationship::MANY_TO_MANY);
    }
}