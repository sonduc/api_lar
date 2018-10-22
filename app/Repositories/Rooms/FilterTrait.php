<?php

namespace App\Repositories\Rooms;

use App\Repositories\GlobalTrait;

trait FilterTrait
{
    use GlobalTrait;

    public function scopeName($query, $q)
    {
        if ($q) {
            $roomColumns      = $this->columnsConverter(['id', 'created_at', 'updated_at']);
            $roomTransColumns = $this->columnsConverter(['name'], 'room_translates', false);
            $columns          = self::mergeUnique($roomColumns, $roomTransColumns);

            $query
                ->addSelect($columns)
                ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
                ->where('room_translates.name', 'like', "%${q}%");
        }
        return $query;
    }

    /**
     * Scope City
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeCity($query, $q)
    {
        if ($q) {
            $query->where('rooms.city_id', $q);
        }

        return $query;
    }

    /**
     * Scope District
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeDistrict($query, $q)
    {
        if ($q) {
            $query->where('rooms.district_id', $q);
        }

        return $query;
    }

    /**
     * Scope Merchant
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeMerchant($query, $q)
    {
        if ($q) {
            $query->where('rooms.merchant_id', $q);
        }

        return $query;
    }

    /**
     * Scope Room Status
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeStatus($query, $q)
    {
        if (array_key_exists($q, $this::ROOM_STATUS)) {
            $query->where('rooms.status', $q);
        }

        return $query;
    }

    /**
     * Scope Manager
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeManager($query, $q)
    {
        if (is_numeric($q) && $q == $this::MANAGER_DEACTIVE) {
            return $query->where('rooms.is_manager', $q);
        }

        return $query->where('rooms.is_manager', $this::MANAGER_ACTIVE);
    }

    /**
     * Kiểu thuê phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeRentType($query, $q)
    {
        if (is_numeric($q)) {
            return $query->where('rooms.rent_type', $q);
        }

        return $query;
    }

    /**
     * Nổi bật
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeHot($query, $q)
    {
        if (is_numeric($q)) {
            return $query->where('rooms.hot', $q);
        }

        return $query;
    }

    /**
     * Mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeNew($query, $q)
    {
        if (is_numeric($q)) {
            return $query->where('rooms.new', $q);
        }

        return $query;
    }

    /**
     * Deal phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeLatestDeal($query, $q)
    {
        if (is_numeric($q)) {
            return $query->where('rooms.latest_deal', $q);
        }

        return $query;
    }

}
