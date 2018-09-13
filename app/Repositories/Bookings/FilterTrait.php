<?php
namespace App\Repositories\Bookings;

use App\Repositories\GlobalTrait;

trait FilterTrait
{
    use GlobalTrait;
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query
                ->where('bookings.name', 'like', "%${q}%")
                ->orWhere('bookings.code', 'like', "%${q}%")
                ->orWhere('bookings.phone', 'like', "%${q}%")
                ->orWhere('bookings.email', 'like', "%${q}%");
        }

        return $query;
    }

    public function scopeMerchants($query, $q)
    {
        if ($q && is_numeric($q)) {
            $query->where('bookings.merchant_id', $q);
        }

        return $query;
    }

    public function scopeCustomers($query, $q)
    {
        if ($q && is_numeric($q)) {
            $query->where('bookings.customer_id', $q);
        }

        return $query;
    }

    public function scopeCity($query, $q)
    {
        if (!self::isJoined($query, 'rooms')) {
            $query->join('rooms', 'rooms.id', '=', 'bookings.room_id');
        }

        if ($q && is_numeric($q)) {
            $query
                ->where('rooms.city_id', $q);
        }

        return $query;
    }

    public function scopeDistrict($query, $q)
    {
        if (!self::isJoined($query, 'rooms')) {
            $query->join('rooms', 'rooms.id', '=', 'bookings.room_id');
        }

        if ($q && is_numeric($q)) {
            $query
                ->where('rooms.district_id', $q);
        }

        return $query;
    }

    public function scopeDateStart($query, $q)
    {
        if ($q)  {
            $query
                ->where('bookings.created_at', '>=', $q);
        }

        return $query;
    }

    public function scopeDateEnd($query, $q)
    {
        if ($q)  {
            $query
                ->where('bookings.created_at', '<=', $q);
        }

        return $query;
    }

}
