<?php

namespace Fluffy\Data\Connector;

use Fluffy\Swoole\Database\PostgreSQLPool;
use Swoole\Coroutine\PostgreSQL;

interface IConnector
{
    function get(): PostgreSQL;
    function getPool(): PostgreSQLPool;
}
