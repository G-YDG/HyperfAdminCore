<?php

declare(strict_types=1);
/**
 * This file is part of HyperfAdminCore.
 *
 *  * @link     https://github.com/G-YDG/HyperfAdminCore
 *  * @license  https://github.com/G-YDG/HyperfAdminCore/blob/master/LICENSE
 */

namespace HyperfAdminCore\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 数据库事务注解。
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Transaction extends AbstractAnnotation
{
    /**
     * @param int $retry 重试次数
     */
    public function __construct(public int $retry = 1)
    {
    }
}
