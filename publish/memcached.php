<?php

declare(strict_types=1);

return [
    'default' => [
        'node' => [
            ['127.0.0.1', '11211'],
        ],
        'user' => '',
        'password' => '',
        'options' => [
            Memcached::OPT_BINARY_PROTOCOL => false, // 开启或关闭二进制协议,SASL鉴权开启后，只有二进制模式可以使用
            Memcached::OPT_DISTRIBUTION => Memcached::DISTRIBUTION_CONSISTENT, // 一致性分布算法(基于libketama)
            Memcached::OPT_LIBKETAMA_COMPATIBLE => true, // 开启或关闭兼容的 libketama 类行为
            Memcached::OPT_NO_BLOCK => true, // 开启或关闭异步 I/O 这将使得存储函数传输速度最大化
            Memcached::OPT_TCP_NODELAY => true, // 开启或关闭已连接socket的无延迟特性（在某些幻境可能会带来速度上的提升）
        ],
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => 60,
        ],
    ],
];
