<?php

declare(strict_types=1);
/**
 * This file is part of HyperfAdminCore.
 *
 *  * @link     https://github.com/G-YDG/HyperfAdminCore
 *  * @license  https://github.com/G-YDG/HyperfAdminCore/blob/master/LICENSE
 */

namespace HyperfAdminCore\Abstracts;

use Hyperf\Context\Context;
use HyperfAdminCore\Model;
use HyperfAdminCore\Traits\MapperTrait;

abstract class AbstractMapper
{
    use MapperTrait;

    /**
     * @var class-string|Model
     */
    public $model;

    public function __construct()
    {
        $this->assignModel();
    }

    /**
     * 魔术方法，从类属性里获取数据.
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    abstract public function assignModel();

    /**
     * 把数据设置为类属性.
     */
    public static function setAttributes(array $data): void
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}
