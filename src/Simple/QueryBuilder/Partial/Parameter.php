<?php
declare(strict_types=1);

namespace Simple\QueryBuilder\Partial;

use Simple\QueryBuilder\EngineInterface;
use Simple\QueryBuilder\StatementInterface;

final class Parameter implements StatementInterface
{
    /** @var string */
    private $sql = '?';

    /** @var array */
    private $params = [];

    public function __construct($value)
    {
        if (is_bool($value) || is_null($value)) {
            $this->sql = var_export($value, true);
        } else {
            $this->params[] = $value;
        }
    }

    public function sql(EngineInterface $engine): string
    {
        return $this->sql;
    }

    public function params(EngineInterface $engine): array
    {
        return $this->params;
    }
}
