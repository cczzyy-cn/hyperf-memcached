<?php

declare(strict_types=1);

namespace Czy\HyperfMemcached;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of memcached client.',
                    'source' => __DIR__ . '/../publish/memcached.php',
                    'destination' => BASE_PATH . '/config/autoload/memcached.php',
                ],
            ],
        ];
    }
}
