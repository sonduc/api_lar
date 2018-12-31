<?php
namespace App\Repositories\Transactions;

use Carbon\Carbon;

trait FilterTrait
{
    public function scopeType($query, $q)
    {
        if ($q) {
            return $query->where('type', $q);
        }
        return $query;
    }

    public function scopeUser($query, $q)
    {
        if ($q) {
            return $query->where('user_id', $q);
        }
        return $query;
    }

    public function scopeRoom($query, $q)
    {
        if ($q) {
            return $query->where('room_id', $q);
        }
        return $query;
    }
    
    public function scopeDate($query, $q)
    {
        if ($q) {
            $q_date = Carbon::parse($q)->toDateTimeString();

            return $query->where('date_create', $q);
        }
        return $query;
    }
}
