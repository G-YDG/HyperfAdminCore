<?php


use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

if (!function_exists('container')) {
    /**
     * 获取容器实例.
     */
    function container(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('redis')) {
    /**
     * 获取Redis实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function redis(): Redis
    {
        return container()->get(Redis::class);
    }
}

if (!function_exists('console')) {
    /**
     * 获取控制台输出实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function console(): StdoutLoggerInterface
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

if (!function_exists('logger')) {
    /**
     * 获取日志实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function logger(string $name = 'Log'): LoggerInterface
    {
        return container()->get(LoggerFactory::class)->get($name);
    }
}

if (!function_exists('env')) {
    /**
     * 获取环境变量信息.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Support\env($key, $default);
    }
}

if (!function_exists('make')) {
    function make(string $name, array $parameters = [])
    {
        return \Hyperf\Support\make($name, $parameters);
    }
}

if (!function_exists('config')) {
    /**
     * 获取配置信息.
     */
    function config(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Config\config($key, $default);
    }
}

if (!function_exists('event')) {
    /**
     * 事件调度快捷方法.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function event(object $dispatch): object
    {
        return container()->get(EventDispatcherInterface::class)->dispatch($dispatch);
    }
}

if (!function_exists('driver')) {
    /**
     * 队列驱动调度快捷方法.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function driver(string $name = 'default'): DriverInterface
    {
        return container()->get(DriverFactory::class)->get($name);
    }
}

if (!function_exists('context_set')) {
    /**
     * 设置上下文数据.
     */
    function context_set(string $key, mixed $data): bool
    {
        return (bool)Context::set($key, $data);
    }
}

if (!function_exists('context_get')) {
    /**
     * 获取上下文数据.
     */
    function context_get(string $key): mixed
    {
        return Context::get($key);
    }
}

if (!function_exists('blank')) {
    /**
     * 判断给定的值是否为空.
     */
    function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (!function_exists('filled')) {
    /**
     * 判断给定的值是否不为空.
     */
    function filled(mixed $value): bool
    {
        return !blank($value);
    }
}

if (!function_exists('format_size')) {
    /**
     * 格式化大小.
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

if (!function_exists('make_dir')) {
    /**
     * 判断文件夹是否存在，不存在则创建.
     */
    function make_dir(string $dir, int $mode = 0700): bool
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }

        if (!mkdir(dirname($dir), $mode)) {
            return false;
        }

        return @mkdir($dir, $mode);
    }
}