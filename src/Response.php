<?php

declare(strict_types=1);
/**
 * This file is part of HyperfAdminCore.
 *
 *  * @link     https://github.com/G-YDG/HyperfAdminCore
 *  * @license  https://github.com/G-YDG/HyperfAdminCore/blob/master/LICENSE
 */

namespace HyperfAdminCore;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;

class Response extends \Hyperf\HttpServer\Response
{
    public function success(?string $message = null, mixed $data = [], int $code = 200): ResponseInterface
    {
        $format = [
            'msg' => $message ?: 'SUCCESS',
            'code' => $code,
            'data' => &$data,
        ];
        return $this->getResponse()
            ->withHeader('Server', env('APP_NAME', 'Hyperf'))
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($this->toJson($format)));
    }

    public function getResponse(): ResponsePlusInterface
    {
        return parent::getResponse();
    }

    public function error(string $message = '', int $code = 500, mixed $data = []): ResponseInterface
    {
        $format = [
            'msg' => $message ?: 'ERROR',
            'code' => $code,
            'data' => &$data,
        ];

        $format = $this->toJson($format);

        return $this->getResponse()
            ->withHeader('Server', env('APP_NAME', 'Hyperf'))
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    /**
     * 向浏览器输出图片.
     */
    public function responseImage(string $image, string $type = 'image/png'): ResponseInterface
    {
        return $this->getResponse()
            ->withHeader('Server', env('APP_NAME', 'Hyperf'))
            ->withAddedHeader('content-type', $type)
            ->withBody(new SwooleStream($image));
    }
}
