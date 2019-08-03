<?php
declare(strict_types=1);

namespace Simple\QueryBuilder\Engine;

use Simple\QueryBuilder\EngineInterface;
use Simple\QueryBuilder\Query;
use Simple\QueryBuilder\StatementInterface;

class BasicEngine implements EngineInterface
{
    public function makeSelect(): Query\SelectQuery
    {
        return new Query\SelectQuery($this);
    }

    public function makeInsert(): Query\InsertQuery
    {
        return new Query\InsertQuery($this);
    }

    public function makeUpdate(): Query\UpdateQuery
    {
        return new Query\UpdateQuery($this);
    }

    public function makeDelete(): Query\DeleteQuery
    {
        return new Query\DeleteQuery($this);
    }

    public function escapeIdentifier(string $identifier): string
    {
        return $identifier;
    }

    public function escapeLike(string $parameter): string
    {
        // Backslash is used to escape wildcards.
        $parameter = str_replace('\\', '\\\\', $parameter);
        // Standard wildcards are underscore and percent sign.
        return str_replace(['%', '_'], ['\\%', '\\_'], $parameter);
    }

    final public function extractParams(): callable
    {
        return function (StatementInterface $statement): array {
            return $statement->params($this);
        };
    }

    final public function extractSql(): callable
    {
        return function (StatementInterface $statement): string {
            return $statement->sql($this);
        };
    }

    final public function flattenParams(StatementInterface ...$statements): array
    {
        return array_merge([], ...array_map($this->extractParams(), $statements));
    }

    final public function flattenSql(string $separator = ' ', StatementInterface ...$statements): string
    {
        return implode($separator, array_map($this->extractSql(), $statements));
    }
}
