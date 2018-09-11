<?php

namespace App\Repositories;

trait GlobalTrait
{
    /**
     * Chuyển đổi các cột về bảng chính
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $columns
     * @param null $table
     * @param bool $fillable
     * @return array
     */
    private function columnsConverter($columns = [], $table = null, $fillable = true)
    {
        if (is_null($table)) {
            $table = $this->table;
        }

        $base = $fillable ? $this->getFillable() : [];

        $base = array_merge($base, $columns);
        foreach ($base as $key => $val)
        {
            $base[$key] = $table.'.'.$val;
        }
        return array_unique($base);
    }

}