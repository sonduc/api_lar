<?php

namespace App\Repositories\Traits;

trait Scope
{
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
        if (count($params)) {
            $reflection = new \ReflectionClass($this->model);
            foreach ($params as $funcName => $funcParams) {
                $funcName = \Illuminate\Support\Str::studly($funcName);
                if ($reflection->hasMethod('scope' . $funcName)) {
                    $funcName = lcfirst($funcName);
                    $this->model   = $this->model->$funcName($funcParams);
                }
            }
        }
    }
}
