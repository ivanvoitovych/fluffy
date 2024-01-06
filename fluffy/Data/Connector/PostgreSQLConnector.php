<?php

namespace Fluffy\Data\Connector;

use Fluffy\Swoole\Database\PostgreSQLPool;
use DotDi\Interfaces\IDisposable;
use Exception;
use Swoole\Coroutine\PostgreSQL;

class PostgreSQLConnector implements IConnector, IDisposable
{
    private PostgreSQL $pg;

    public function __construct(private PostgreSQLPool $connectionPool)
    {
    }

    public function get(): PostgreSQL
    {
        return $this->pg ?? ($this->pg = $this->connectionPool->get());
    }

    public function getPool(): PostgreSQLPool
    {
        return $this->connectionPool;
    }

    public function dispose()
    {
        if (isset($this->pg)) {
            $this->connectionPool->put($this->pg);
            unset($this->pg);
        }
    }
}
