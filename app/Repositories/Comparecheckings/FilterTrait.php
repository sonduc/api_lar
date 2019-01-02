<?php
namespace App\Repositories\CompareCheckings;

use Carbon\Carbon;

trait FilterTrait
{
    public function scopeUser($query, $q)
    {
        if ($q) {
            return $query->where('user_id', $q);
        }
        return $query;
    }

    public function scopeDate($query, $q)
    {
        if ($q) {
            $q_date = Carbon::parse($q)->toDateString();

            return $query->where('date', $q);
        }
        return $query;
    }
}
