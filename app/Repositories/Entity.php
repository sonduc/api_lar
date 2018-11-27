<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    /**
     * order by query
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  [type]     $query [description]
     * @param  string $sort [created_at:-1,id:-1]
     *
     * @return [type]            [description]
     */
    public function scopeSort($query, $sort = 'created_at:-1')
    {
        $sorts = explode(',', $sort);
        foreach ($sorts as $sort) {
            $sort = explode(':', $sort);
            list($field, $type) = [array_get($sort, '0', 'created_at'), array_get($sort, '1', 1)];
            $query->orderBy($field, $type == 1 ? 'ASC' : 'DESC');
        }
        return $query;
    }
}
