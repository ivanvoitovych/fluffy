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
            $broken = $this->pg->error && !$this->pg->resultDiag['sqlstate'];
            // echo "PUT connection $broken" . PHP_EOL;
            $this->connectionPool->put($broken ? null : $this->pg);
            unset($this->pg);
        }
    }
}
