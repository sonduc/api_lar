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

    public function scopeMerchant($query, $q)
    {
        if ($q) {
            return $query->where('user_id', $q);
        }
        return $query;
    }

    public function scopeProperty($query, $q)
    {
        if ($q) {
            return $query->where('room_id', $q);
        }
        return $query;
    }
    
    public function scopeCheckingStart($query, $q)
    {
        if ($q) {
            $q_date = Carbon::parse($q)->toDateString();

            return $query->where('date_create', '>=', $q_date);
        }
        return $query;
    }
    public function scopeCheckingEnd($query, $q)
    {
        if ($q) {
            $q_date = Carbon::parse($q)->toDateString();

            return $query->where('date_create', '<=', $q_date);
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
