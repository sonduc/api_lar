<?php

namespace App\Repositories\Bookings;

use App\Repositories\GlobalTrait;
use Carbon\Carbon;

trait FilterTrait
{
    use GlobalTrait;

    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('bookings.name', 'like', "%${q}%")
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
            $query->join('rooms', 'bookings.room_id', '=', 'rooms.id')->select('bookings.*');
        }

        if ($q && is_numeric($q)) {
            $query->where('rooms.city_id', $q)->addSelect('rooms.city_id');
        }
//        dd($query->toSql());
        return $query;
    }

    public function scopeDistrict($query, $q)
    {
        if (!self::isJoined($query, 'rooms')) {
            $query->join('rooms', 'rooms.id', '=', 'bookings.room_id')->select('bookings.*');
        }

        if ($q && is_numeric($q)) {
            $query->where('rooms.district_id', $q)->addSelect('rooms.district_id');
        }
        return $query;
    }

    public function scopeDateStart($query, $q)
    {
        if ($q) {
            $query->where('bookings.created_at', '>=', $q);
        }

        return $query;
    }

    public function scopeDateEnd($query, $q)
    {
        if ($q) {
            $query->where('bookings.created_at', '<=', $q);
        }

        return $query;
    }

    public function scopeDateIn($query, $q)
    {
        if ($q) {
            $q_timestamp = Carbon::parse($q)->timestamp;
            $query->where('bookings.checkin', '>=', $q_timestamp);
        }

        return $query;
    }

    public function scopeDateOut($query, $q)
    {
        if ($q) {
            $q_timestamp = Carbon::parse($q)->timestamp;
            $query->where('bookings.checkout', '<=', $q_timestamp);
        }

        return $query;
    }

    public function scopePaymentStatus($query, $q)
    {
        if ($q && is_numeric($q)) {
            $query->where('bookings.payment_status', $q);
        }

        return $query;
    }

    public function scopeBookingType($query, $q)
    {
        if ($q && is_numeric($q)) {
            $query->where('bookings.booking_type', $q);
        }

        return $query;
    }

    public function scopeSource($query, $q)
    {
        if ($q && is_numeric($q)) {
            $query->where('bookings.source', $q);
        }

        return $query;
    }

    public function scopeStatus($query, $q)
    {
        if ($q && is_numeric($q)) {
            $query->where('bookings.status', $q);
        }

        return $query;
    }

    public function scopeRooms($query, $q)
    {
        if ($q) {
            $query->where('bookings.room_id', '=', $q);
        }

        return $query;
    }
}
