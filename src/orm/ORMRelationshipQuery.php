<?php

namespace Sherpa\Trail\orm;

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
     */
    public function prepareResult(): mixed
    {
        if (Relationship::BELONGS_TO || Relationship::HAS_ONE)
        {
            return $this->first();
        }
        else
        {
            return $this->get();
        }
    }
}