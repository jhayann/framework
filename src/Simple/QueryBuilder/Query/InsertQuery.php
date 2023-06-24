<?php

declare(strict_types=1);

namespace Simple\QueryBuilder\Query;

use Simple\QueryBuilder\ExpressionInterface;
use Simple\QueryBuilder\StatementInterface;

use function array_keys;
use function array_values;
use function Simple\QueryBuilder\express;
use function Simple\QueryBuilder\identify;
use function Simple\QueryBuilder\identifyAll;
use function Simple\QueryBuilder\listing;
use function Simple\QueryBuilder\paramAll;

class InsertQuery extends AbstractQuery
{
    protected StatementInterface $into;
    protected ?StatementInterface $columns = null;
    protected array $values = [];

    /**
     * @param mixed $table
     */
    public function into($table): self
    {
        $this->into = identify($table);

        return $this;
    }

    public function map(array $map): self
    {
        return $this->columns(...array_keys($map))->values(...array_values($map));
    }

    /**
     * @param mixed ...$columns
     */
    public function columns(...$columns): self
    {
        $this->columns = listing(identifyAll($columns));

        return $this;
    }

    /**
     * @param mixed ...$params
     */
    public function values(...$params): self
    {
        $this->values[] = express('(%s)', listing(paramAll($params)));

        return $this;
    }

    public function asExpression(): ExpressionInterface
    {
        $query = $this->startExpression();
        $query = $this->applyInto($query);
        $query = $this->applyColumns($query);
        $query = $this->applyValues($query);

        return $query;
    }

    protected function startExpression(): ExpressionInterface
    {
        return express('INSERT');
    }

    protected function applyInto(ExpressionInterface $query): ExpressionInterface
    {
        return $this->into ? $query->append('INTO %s', $this->into) : $query;
    }

    protected function applyColumns(ExpressionInterface $query): ExpressionInterface
    {
        return $this->columns ? $query->append('(%s)', $this->columns) : $query;
    }

    protected function applyValues(ExpressionInterface $query): ExpressionInterface
    {
        return $this->values ? $query->append('VALUES %s', listing($this->values)) : $query;
    }
}
