<?php

namespace Sherpa\Trail\orm;

use Sherpa\Db\database\Query;

class ORMQuery extends Query
{
    /** Default columns selection if none is provided. */
    private const array DEFAULT_COLUMNS = ["*"];

    private array $publicData = [];
    private array $hiddenData = [];
}