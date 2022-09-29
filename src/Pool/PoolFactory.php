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
namespace Czy\HyperfMemcached\Pool;

use Hyperf\Di\Container;
use Psr\Container\ContainerInterface;

class PoolFactory
{
    /**
     * @var MemcachedPool[]
     */
    protected array $pools = [];

    public function __construct(protected ContainerInterface $container)
    {
    }

    public function getPool(string $name): MemcachedPool
    {
        if (isset($this->pools[$name])) {
            return $this->pools[$name];
        }

        if ($this->container instanceof Container) {
            $pool = $this->container->make(MemcachedPool::class, ['name' => $name]);
        } else {
            $pool = new MemcachedPool($this->container, $name);
        }
        return $this->pools[$name] = $pool;
    }
}
