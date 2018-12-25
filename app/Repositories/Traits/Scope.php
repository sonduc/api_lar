<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Scope
{
    /** @var Builder */
    protected $model;

    /**
     * Dùng scope cho database
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $params
     * @param array $except
     *
     * @throws \ReflectionException
     */
    public function useScope($params = [], $except = []): void
    {
        $except = array_merge(['page', 'limit'], $except);;
        $params = array_except($params, $except);
        if (\count($params)) {
            $reflection = new \ReflectionClass($this->model);
            foreach ($params as $funcName => $funcParams) {
                $funcName = \Illuminate\Support\Str::studly($funcName);
                if ($reflection->hasMethod('scope' . $funcName)) {
                    $funcName    = lcfirst($funcName);
                    $this->model = $this->model->$funcName($funcParams);
                }
            }
        }
    }

    /**
     * Use eager loading for transformer
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $params
     * @param array $alias
     */
    public function eagerLoadWithTransformer($params, $alias = []): void
    {
        $list   = collect($params);
        $nested = explode(',', $list->get('include'));
        $method = get_class_methods($this->model->getModel());

        if ($alias) {
            $nested = array_map(function ($item) use ($alias) {
                if (array_key_exists($item, $alias)) {
                    return $alias[$item];
                }
                return $item;
            }, $nested);
        }

        $nested = array_intersect($nested, $method);
        $this->model->with($nested);
    }
}
