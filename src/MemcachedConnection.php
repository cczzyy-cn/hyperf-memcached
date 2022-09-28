<?php

declare(strict_types=1);

namespace Czy\HyperfMemcached;

use Czy\HyperfMemcached\Exception\MemcachedException;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\PoolInterface;
use Hyperf\Pool\Connection;
use Memcached;
use Psr\Container\ContainerInterface;

/**
 * MemcachedConnection.
 */
class MemcachedConnection extends Connection implements ConnectionInterface
{
    protected Memcached|null $connection = null;

    protected array $config = [
        'node' => [
            ['127.0.0.1', '11211'],
        ],
        'user' => null,
        'password' => null,
        'options' => [
            Memcached::OPT_BINARY_PROTOCOL => false, // 二进制协议,SASL开启后，只有二进制模式可以使用
            Memcached::OPT_DISTRIBUTION => Memcached::DISTRIBUTION_CONSISTENT, // 一致性分布算法(基于libketama)
            Memcached::OPT_LIBKETAMA_COMPATIBLE => true, // 开启或关闭兼容的 libketama 类行为
            Memcached::OPT_NO_BLOCK => true, // 开启或关闭异步 I/O 这将使得存储函数传输速度最大化
            Memcached::OPT_TCP_NODELAY => true, // 开启或关闭已连接socket的无延迟特性（在某些幻境可能会带来速度上的提升）
        ],
    ];

    public function __construct(ContainerInterface $container, PoolInterface $pool, array $config)
    {
        parent::__construct($container, $pool);
        $this->config = array_replace_recursive($this->config, $config);
    }

    public function createMemcached(): Memcached
    {
        $bool = extension_loaded('Memcached');
        if (! $bool) {
            throw new MemcachedException('PHP 未安装 Memcached 扩展');
        }
        $memcached = new Memcached();
        $memcached->addServers($this->config['node']);
        $memcached->setOptions($this->config['options']);
        if (! empty($this->config['user']) && ! empty($this->config['password'])) {
            $memcached->setSaslAuthData($this->config['user'], $this->config['password']);
        }
        $res = $memcached->getLastErrorMessage();
        if ($res != 'SUCCESS') {
            throw new MemcachedException($res);
        }
        return $memcached;
    }

    public function reconnect(): bool
    {
        $this->connection = $this->createMemcached();
        $this->lastUseTime = microtime(true);
        return true;
    }

    public function getActiveConnection(): ?Memcached
    {
        if (! $this->connection || ! $this->check()) {
            $this->reconnect();
        }
        return $this->connection;
    }

    public function close(): bool
    {
        $this->connection = null;
        return true;
    }
}
