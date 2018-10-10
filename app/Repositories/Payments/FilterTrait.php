<?php

namespace App\Repositories\Payments;

trait FilterTrait
{
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('name', 'like', "%${q}%");
        }
        return $query;
    }

    public function scopeBooking($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('payment_histories.booking_id', $q);
        }
        return $query;
    }

    public function scopeStatus($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('payment_histories.status', $q);
        }
        return $query;
    }

    public function scopeConfirm($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('payment_histories.confirm', $q);
        }
        return $query;
    }


}
