<?php

namespace Fluffy\Swoole\Database;

use RuntimeException;

/**
 * Thrown when a pooled connection could not be obtained within the pool's wait timeout.
 *
 * Deliberately distinct from a connect failure: the server on the other end may be perfectly
 * healthy and the POOL simply drained, so the message names the pool and its counters rather
 * than the database. If you are reading this in a stack trace, the question is not "is PostgreSQL
 * up" but "who borrowed a connection and never gave it back".
 */
class PoolTimeoutException extends RuntimeException {}
