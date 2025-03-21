<?php

namespace Sherpa\Trail\orm;

use Sherpa\Core\exceptions\database\RelationshipDoesNotExistOnModelException;
use Sherpa\Core\models\Model;
use Sherpa\Db\database\DB;
use Sherpa\Db\database\Query;
use Sherpa\Trail\entities\Collection;

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
     * @throws RelationshipDoesNotExistOnModelException
     */
    public function use(array|string $rels): self
    {
        if (is_string($rels))
        {
            if (!method_exists($this->model, $rels))
            {
                throw new RelationshipDoesNotExistOnModelException(
                    $rels, $this->model);
            }

            $this->relationships[] = $rels;
        }
        else
        {
            foreach ($rels as $rel)
            {
                if (!method_exists($this->model, $rel))
                {
                    throw new RelationshipDoesNotExistOnModelException(
                        $rel, $this->model);
                }
            }

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
    public function get(array $columns = ["*"]): Collection
    {
        $sql = $this->sql();
        $parameters = $this->parameters;

        $rows = DB::run($sql, $parameters);

        // Field is hidden by default
        $result = array_map(function ($row)
        {
            $public = array_intersect_key(
                $row, array_flip($this->publicData));

            $private = array_diff_key(
                $row, array_flip($this->publicData));

            $modelObject = new $this->model($public, $private);

            foreach ($this->relationships as $relationship)
            {
                $modelObject->data->$relationship
                    = $modelObject->$relationship()
                                  ->prepareResult();
            }

            return $modelObject;
        }, $rows);

        return new Collection($result);
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
        return $this->get($columns)->first();
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
        return $this->get($columns)->last();
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


    /**
     * Convert as ORMRelationshipQuery class object.
     *
     * @param Relationship $relationship Relationship type
     * @return ORMRelationshipQuery
     * @see ORMRelationshipQuery
     */
    public function toRelationshipQuery(Relationship $relationship): ORMRelationshipQuery
    {
        $relQuery = new ORMRelationshipQuery(
            $this->model,
            $this->publicData,
            $this->hiddenData,
            $relationship
        );

        $relQuery->columns = $this->columns;
        $relQuery->parameters = $this->parameters;
        $relQuery->conditions = $this->conditions;
        $relQuery->orderBy = $this->orderBy;
        $relQuery->having = $this->having;
        $relQuery->groupBy = $this->groupBy;
        $relQuery->joins = $this->joins;
        $relQuery->limit = $this->limit;
        $relQuery->offset = $this->offset;

        return $relQuery;
    }

    public function create(array $data): mixed
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $this->parameters = array_values($data);

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $this->table,
            implode(", ", $columns),
            implode(", ", $placeholders));

        DB::run($sql, $this->parameters);

        return ($this->model)::query()
                             ->find(DB::lastInsertId());
    }
}