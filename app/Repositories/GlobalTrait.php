<?php

namespace App\Repositories;

trait GlobalTrait
{
    public static function isJoined($query, $table = null)
    {
        $joins = collect($query->getQuery()->joins);
        return $joins->pluck('table')->contains($table);
    }
    
    /**
     * Chuyển đổi các cột về bảng chính
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $columns
     * @param null  $table
     * @param bool  $fillable
     *
     * @return array
     */
    private function columnsConverter($columns = [], $table = null, $fillable = true)
    {
        if (is_null($table)) {
            $table = $this->table;
        }
        
        $base = $fillable ? $this->getFillable() : [];
        
        $base = self::mergeUnique($base, $columns);
        
        foreach ($base as $key => $val) {
            $base[$key] = $table . '.' . $val;
        }
        return array_unique($base);
    }
    
    public static function mergeUnique(...$array)
    {
        $result = [];
        foreach ($array as $t) {
            $result = array_merge($result, $t);
        }
        return array_unique($result);
    }
}