<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Czy\HyperfMemcached;

use Czy\HyperfMemcached\Exception\MemcachedException;
use Czy\HyperfMemcached\Pool\PoolFactory;

/**
 * Memcached.
 * @mixin \Memcached
 */
class Memcached
{
    protected string $poolName = 'default';

    public function __construct(protected PoolFactory $factory)
    {
    }

    public function __call($name, $arguments)
    {
        $memcachedPool = $this->factory->getPool($this->poolName);
        $connection = $memcachedPool->get();
        $memcached = $connection->getConnection();
        $result = $memcached->{$name}(...$arguments);
        $msg = $memcached->getLastErrorMessage();
        if ($msg != 'SUCCESS') {
            throw new MemcachedException($msg);
        }
        $connection->release();
        return $result;
    }
}
