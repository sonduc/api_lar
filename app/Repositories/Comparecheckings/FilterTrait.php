<?php
namespace App\Repositories\CompareCheckings;

use Carbon\Carbon;

trait FilterTrait
{
    public function scopeMerchant($query, $q)
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

    public function scopeDateStart($query, $q)
    {
        if ($q) {
            $q_date = Carbon::parse($q)->startOfDay()->toDateTimeString();

            return $query->where('created_at', '>=', $q_date);
        }
        return $query;
    }
    public function scopeDateEnd($query, $q)
    {
        if ($q) {
            $q_date = Carbon::parse($q)->endOfDay()->toDateTimeString();

            return $query->where('created_at', '<=', $q_date);
        }
        return $query;
    }
}
