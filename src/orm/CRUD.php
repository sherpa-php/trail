<?php

namespace Sherpa\Trail\orm;

trait CRUD
{
    /** Default columns selection if none is provided. */
    private const array DEFAULT_COLUMNS = ["*"];

    private array $publicData = [];
    private array $hiddenData = [];

    public static function get(?array $columns = null): self
    {
        $columns = $columns ?? self::DEFAULT_COLUMNS;

        $object = new self();

        return $object;
    }
}