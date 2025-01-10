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
class ORMQuery extends Query
{
    /** Default columns selection if none is provided. */
    private const array DEFAULT_COLUMNS = ["*"];

    private string $model;

    private array $publicData = [];
    private array $hiddenData = [];

    private array $relationships = [];

    public function __construct(
        string $model,
        array $publicData,
        array $hiddenData)
    {
        parent::__construct($model::table());

        $this->model = $model;
        $this->publicData = $publicData;
        $this->hiddenData = $hiddenData;
    }

    /**
     * Use a defined relationship.
     *
     * @param array|string $rels
     * @return $this
     */
    public function use(array|string $rels): self
    {
        if (is_string($rels))
        {
            $this->relationships[] = $rels;
        }
        else
        {
            $this->relationships = array_merge($this->relationships, $rels);
        }

        return $this;
    }

    /**
     * Get query's result's rows as array of objects.
     *
     * @param array $columns (optional) not hidden columns to return;
     *                       by default, all not hidden columns are returned
     * @return array
     */
    public function get(array $columns = ["*"]): array
    {
        $sql = $this->sql();
        $parameters = $this->parameters;

        $rows = DB::run($sql, $parameters);

        // Field is hidden by default
        $filteredRows = array_map(function ($row)
        {
            $modelObject = new $this->model();

            $relationships = [];

            foreach ($this->relationships as $relationship)
            {
                $relationships[$relationship]
                    = $modelObject->$relationship();
            }

            $data = array_merge(
                array_intersect_key($row, array_flip($this->publicData)),
                $relationships
            );

            $modelObject->data = json_decode(json_encode($data));

            return $modelObject;
        }, $rows);

        return $filteredRows;
    }

    /**
     * Get first row from query's result's rows array.
     *
     * @param array $columns (optional) not hidden columns to return;
     *                       by default, all not hidden columns are returned
     * @return object|null First row if exists; else NULL
     */
    public function first(array $columns = ["*"]): mixed
    {
        return $this->get($columns)[0] ?? null;
    }

    /**
     * Get last row from query's result's rows array.
     *
     * @param array $columns (optional) not hidden columns to return;
     *                       by default, all not hidden columns are returned
     * @return object|null Last row if exists; else NULL
     */
    public function last(array $columns = ["*"]): mixed
    {
        $rows = $this->get($columns);

        return array_pop($rows);
    }

    /**
     * Get row using its primary key (id).
     *
     * @param mixed $id Primary key value
     * @param array $columns (optional) not hidden columns to return;
     *                       by default, all not hidden columns are returned
     * @param string $idColumn (optional) Primary key column's name;
     *                         by default, "id"
     * @return object|null First row if exists; else NULL
     */
    public function find(mixed $id, array $columns = ["*"], string $idColumn = "id"): ?object
    {
        $this->where($idColumn, $id);

        return $this->first($columns);
    }
}