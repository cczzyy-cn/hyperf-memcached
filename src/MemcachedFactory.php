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
use Hyperf\Contract\ConfigInterface;

class MemcachedFactory
{
    /**
     * @var MemcachedProxy[]
     */
    protected array $proxies = [];

    public function __construct(ConfigInterface $config)
    {
        $memcachedConfig = $config->get('memcached');

        foreach ($memcachedConfig as $poolName => $item) {
            $this->proxies[$poolName] = make(MemcachedProxy::class, ['pool' => $poolName]);
        }
    }

    public function get(string $poolName): MemcachedProxy
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (! $proxy instanceof MemcachedProxy) {
            throw new MemcachedException('Invalid Memcached proxy.');
        }

        return $proxy;
    }
}
