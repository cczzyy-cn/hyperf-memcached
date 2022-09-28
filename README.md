
## 安装组件
```bash
composer require czy/hyperf-memcached
```

## 生成配置文件
```bash
php bin/hyperf.php vendor:publish czy/hyperf-memcached
```

## 修改配置 config/autoload/memcached.php
```php
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
    'memcached-b' => [
        'node' => [
            ['host-b', '11211'],
        ],
        'user' => '',
        'password' => '',
    ],
];

```

## 控制器中使用
```php
<?php

declare(strict_types=1);

/**
 * Author czy
 * Date 2022/9/13 18:41
 */

namespace App\Controller;

use Czy\HyperfMemcached\Memcached;
use Czy\HyperfMemcached\MemcachedFactory;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * Memcached 控制器
 * MemController
 */
#[Controller(prefix: "Mem")]
class MemController extends AbstractController
{
    /**
     * 注入默认连接池 default
     * @var Memcached
     */
    #[Inject]
    protected Memcached $memcached;

    /**
     * 工厂获取的连接池 memcached-b
     * @var Memcached
     */
    protected Memcached $memcachedFactory;

    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->memcachedFactory = $container->get(MemcachedFactory::class)->get('memcached-b');
    }

    /**
     * 设置
     *
     * Author czy
     * Date 2022/9/14 1:48
     * @return Psr7ResponseInterface
     */
    #[RequestMapping(path: "set", methods: ["GET", "POST"])]
    public function set(): Psr7ResponseInterface
    {
        $reqData = $this->request->all();
        $data = $this->memcached->set($reqData['key'], $reqData['value'], (int)$reqData['time'] ?? 60);
        $res = [
            'reqData' => $reqData,
            'data' => $data,
        ];
        return $this->responseJson($res);
    }

    /**
     * 获取
     *
     * Author czy
     * Date 2022/9/14 1:49
     * @return Psr7ResponseInterface
     */
    #[RequestMapping(path: "get", methods: ["GET", "POST"])]
    public function get(): Psr7ResponseInterface
    {
        $reqData = $this->request->all();
        $data = $this->memcached->get($reqData['key']);
        $res = [
            'reqData' => $reqData,
            'data' => $data,
        ];
        return $this->responseJson($res);
    }

    /**
     * 获取 Memcached 服务器状态
     *
     * Author czy
     * Date 2022/9/14 1:49
     * @return Psr7ResponseInterface
     */
    #[RequestMapping(path: "getStats", methods: ["GET", "POST"])]
    public function getStats(): Psr7ResponseInterface
    {
        $reqData = $this->request->all();
        // getStats()方法会使连接断开
        $data = $this->memcached->getStats();
        $res = [
            'reqData' => $reqData,
            'data' => $data,
        ];
        return $this->responseJson($res);
    }

    /**
     * 设置
     *
     * Author czy
     * Date 2022/9/14 1:48
     * @return Psr7ResponseInterface
     */
    #[RequestMapping(path: "setByFactory", methods: ["GET", "POST"])]
    public function setByFactory(): Psr7ResponseInterface
    {
        $reqData = $this->request->all();
        $data = $this->memcachedFactory->set($reqData['key'], $reqData['value'], (int)$reqData['time'] ?? 60);
        $res = [
            'reqData' => $reqData,
            'data' => $data,
        ];
        return $this->responseJson($res);
    }

    /**
     * 获取
     *
     * Author czy
     * Date 2022/9/14 1:49
     * @return Psr7ResponseInterface
     */
    #[RequestMapping(path: "getByFactory", methods: ["GET", "POST"])]
    public function getByFactory(): Psr7ResponseInterface
    {
        $reqData = $this->request->all();
        $data = $this->memcachedFactory->get($reqData['key']);
        $res = [
            'reqData' => $reqData,
            'data' => $data,
        ];
        return $this->responseJson($res);
    }
}
```
