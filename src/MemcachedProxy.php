<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-memcached.
 *
 * @link     https://github.com/Cheng-ZY/hyperf-memcached
 * @document https://github.com/Cheng-ZY/hyperf-memcached
 * @contact  cczzyy.cn@gmail.com
 * @license  https://github.com/Cheng-ZY/hyperf-memcached/blob/main/LICENSE
 */
namespace Czy\HyperfMemcached;

use Czy\HyperfMemcached\Pool\PoolFactory;

/**
 * @mixin \Memcached
 */
class MemcachedProxy extends Memcached
{
    public function __construct(PoolFactory $factory, string $pool)
    {
        parent::__construct($factory);
        $this->poolName = $pool;
    }

    /**
     * WARN: Can't remove this function, because AOP need it.
     * @see https://github.com/hyperf/hyperf/issues/1239
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        return parent::__call($name, $arguments);
    }
}
