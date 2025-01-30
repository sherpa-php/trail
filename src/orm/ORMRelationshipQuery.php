<?php

namespace Sherpa\Trail\orm;

use Sherpa\Core\exceptions\database\InvalidRelationshipException;
use Sherpa\Core\models\Model;
use Sherpa\Db\database\DB;
use Sherpa\Db\database\Query;

/**
 * ORM Query main class.
 * <p>
 *     Allows to make query from model class using Sherpa ORM.
 * </p>
 */
class ORMRelationshipQuery extends ORMQuery
{
    public private(set) Relationship $relationship;

    public function __construct(
        string $model,
        array $publicData,
        array $hiddenData,
        Relationship $relationship)
    {
        parent::__construct($model, $publicData, $hiddenData);

        $this->relationship = $relationship;
    }

    /**
     * Get prepared relationship's query's result.
     * <p>
     *     The result is based on relationship data convention.
     * </p>
     * <ul>
     *     <li>Has-One and Belongs-To = first related model object</li>
     *     <li>Has-Many and Many-to-Many = related model objects array</li>
     * </ul>
     *
     * @return mixed Prepared result
     * @throws InvalidRelationshipException
     */
    public function prepareResult(): mixed
    {
        $result = $this->get();

        return match ($this->relationship)
        {
            Relationship::BELONGS_TO,
            Relationship::HAS_ONE => $this
                ->first()
                ->data,

            Relationship::HAS_MANY,
            Relationship::MANY_TO_MANY => array_map(function ($row)
                {
                    return $row->data;
                }, $result),

            default => throw new InvalidRelationshipException(
                $this->relationship),
        };
    }
}