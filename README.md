## 添加组件
```bash
composer require czy/hyperf-memcached
```

## 生成配置文件

```bash
php bin/hyperf.php vendor:publish czy/hyperf-memcached
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
     * 注入默认连接池
     * @var Memcached
     */
    #[Inject]
    protected Memcached $memcached;

    /**
     * 工厂获取的 memcached-b 连接池
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
