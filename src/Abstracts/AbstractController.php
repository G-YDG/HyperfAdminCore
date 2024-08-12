<?php

declare(strict_types=1);
/**
 * This file is part of HyperfAdminCore.
 *
 *  * @link     https://github.com/G-YDG/HyperfAdminCore
 *  * @license  https://github.com/G-YDG/HyperfAdminCore/blob/master/LICENSE
 */

namespace HyperfAdminCore\Abstracts;

use Hyperf\Di\Annotation\Inject;
use HyperfAdminCore\Request;
use HyperfAdminCore\Response;
use HyperfAdminCore\Traits\ControllerTrait;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    use ControllerTrait;

    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected Request $request;

    #[Inject]
    protected Response $response;

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
