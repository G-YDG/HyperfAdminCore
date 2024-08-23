<?php

declare(strict_types=1);
/**
 * This file is part of HyperfAdminCore.
 *
 *  * @link     https://github.com/G-YDG/HyperfAdminCore
 *  * @license  https://github.com/G-YDG/HyperfAdminCore/blob/master/LICENSE
 */

namespace HyperfAdminCore\Traits;

use Closure;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\ModelCache\Manager;
use HyperfAdminCore\Model;

trait MapperTrait
{
    /**
     * @var Model
     */
    public $model;

    /**
     * 获取列表数据.
     */
    public function getList(?array $params): array
    {
        return $this->listQuerySetting($params)->get()->toArray();
    }

    /**
     * 闭包通用方式查询数据集合.
     * @param array|string[] $column
     */
    public function get(?Closure $closure = null, array $column = ['*']): array
    {
        return $this->settingClosure($closure)->get($column)->toArray();
    }

    /**
     * 闭包通用查询设置.
     * @param null|Closure $closure 传入的闭包查询
     */
    public function settingClosure(?Closure $closure = null): Builder
    {
        return $this->model::where(function ($query) use ($closure) {
            if ($closure instanceof Closure) {
                $closure($query);
            }
        });
    }

    /**
     * 返回模型查询构造器.
     */
    public function listQuerySetting(?array $params): Builder
    {
        $query = $this->model::query();

        if ($params['select'] ?? false) {
            $query->select($this->filterQueryAttributes($params['select']));
        }

        if ($params['with'] ?? false) {
            $query->with($params['with']);
        }

        $query = $this->handleOrder($query, $params);

        return $this->handleSearch($query, $params);
    }

    /**
     * 过滤查询字段不存在的属性.
     */
    public function filterQueryAttributes(array $fields, bool $removePk = false): array
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($fields as $key => $field) {
            if (! in_array(trim($field), $attrs) && mb_strpos(str_replace('AS', 'as', $field), 'as') === false) {
                unset($fields[$key]);
            } else {
                $fields[$key] = trim($field);
            }
        }
        if ($removePk && in_array($model->getKeyName(), $fields)) {
            unset($fields[array_search($model->getKeyName(), $fields)]);
        }
        $model = null;
        return (count($fields) < 1) ? ['*'] : $fields;
    }

    /**
     * 排序处理器.
     */
    public function handleOrder(Builder $query, ?array &$params = null): Builder
    {
        // 对树型数据强行加个排序
        if (isset($params['_tree'])) {
            $query->orderBy($params['_tree_pid']);
        }

        if ($params['orderBy'] ?? false) {
            if (is_array($params['orderBy'])) {
                foreach ($params['orderBy'] as $key => $order) {
                    $query->orderBy($order, $params['orderType'][$key] ?? 'asc');
                }
            } else {
                if (isset($params['orderType']) && in_array($params['orderType'], ['ascend', 'descend'])) {
                    $params['orderType'] = $params['orderType'] === 'ascend' ? 'asc' : 'desc';
                }
                $query->orderBy($params['orderBy'], $params['orderType'] ?? 'asc');
            }
        }

        return $query;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, ?array $params): Builder
    {
        return $query;
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params, string $pageName = 'page'): array
    {
        $paginate = $this->listQuerySetting($params)->paginate(
            intval($params['pageSize'] ?? $this->model::PAGE_SIZE),
            ['*'],
            $pageName,
            intval($params[$pageName] ?? 1),
        );
        return $this->setPaginate($paginate, $params);
    }

    /**
     * 设置数据库分页.
     */
    public function setPaginate(LengthAwarePaginatorInterface $paginate, array $params = []): array
    {
        return [
            'items' => method_exists($this, 'handlePageItems') ? $this->handlePageItems($paginate->items(), $params) : $paginate->items(),
            'pageInfo' => [
                'total' => $paginate->total(),
                'currentPage' => $paginate->currentPage(),
                'totalPage' => $paginate->lastPage(),
            ],
        ];
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    /**
     * 过滤新增或写入不存在的字段.
     */
    public function filterExecuteAttributes(array &$data, bool $removePk = false): void
    {
        $model = new $this->model();
        $attrs = $model->getFillable();
        foreach ($data as $name => $val) {
            if (! in_array($name, $attrs)) {
                unset($data[$name]);
            }
        }
        if ($removePk && isset($data[$model->getKeyName()])) {
            unset($data[$model->getKeyName()]);
        }
        $model = null;
    }

    public function getModel(): Model
    {
        return new $this->model();
    }

    /**
     * 读取一条数据.
     */
    public function read(int $id, array $column = ['*']): ?Model
    {
        return ($model = $this->model::find($id, $column)) ? $model : null;
    }

    /**
     * 获取单个值
     */
    public function value(array $condition, string $columns = 'id'): ?Model
    {
        return ($model = $this->model::where($condition)->value($columns)) ? $model : null;
    }

    /**
     * 获取单列值
     */
    public function pluck(array $condition, string $columns = 'id', ?string $key = null): array
    {
        return $this->model::where($condition)->pluck($columns, $key)->toArray();
    }

    /**
     * 记录是否存在.
     */
    public function exist(array $condition): bool
    {
        return $this->model::where($condition)->exists();
    }

    /**
     * 按条件更新数据.
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::query()->where($condition)->update($data) > 0;
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        return $this->model::find($id)->update($data) > 0;
    }

    /**
     * 单个或批量删除数据.
     */
    public function delete(array $ids): bool
    {
        $this->model::destroy($ids);

        $manager = ApplicationContext::getContainer()->get(Manager::class);
        $manager->destroy($ids, $this->model);

        return true;
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(
        ?array $params = null,
        string $id = 'id',
        string $parentField = 'parent_id',
        string $children = 'children'
    ): array {
        $params['_tree'] = true;
        $params['_tree_pid'] = $parentField;
        $data = $this->listQuerySetting($params)->get();
        return $data->toTree([], $data[0]->{$parentField} ?? 0, $id, $parentField, $children);
    }

    /**
     * 闭包通用方式查询一条数据.
     * @param array|string[] $column
     */
    public function one(?Closure $closure = null, array $column = ['*'])
    {
        return $this->settingClosure($closure)->select($column)->first();
    }

    /**
     * 按条件读取一行数据.
     */
    public function first(array $condition, array $column = ['*']): ?Model
    {
        return ($model = $this->model::where($condition)->first($column)) ? $model : null;
    }

    /**
     * 闭包通用方式统计
     */
    public function count(?Closure $closure = null, string $column = '*'): int
    {
        return $this->settingClosure($closure)->count($column);
    }

    /**
     * 闭包通用方式查询最大值
     * @return mixed|string|void
     */
    public function max(?Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->max($column);
    }

    /**
     * 闭包通用方式查询最小值
     * @return mixed|string|void
     */
    public function min(?Closure $closure = null, string $column = '*')
    {
        return $this->settingClosure($closure)->min($column);
    }
}
