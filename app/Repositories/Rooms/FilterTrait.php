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
        if (is_numeric($q)) {
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
        if (is_numeric($q)) {
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
        if (is_numeric($q)) {
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
     * Kiểu cho thuê phòng (theo ngày , theo giờ, cả ngày và giờ)
     * @author ducchien0612 <ducchien0612@gmail.com>
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
     * Scope latest_deal
     * @author ducchien0612 <ducchien0612@gmail.com>
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
    }

    /**
     * Kiểu phòng (theo căn hộ ,nhà riêng, phòng riêng)
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeTypeRoom($query, $q)
    {
        if (is_numeric($q)) {
            return $query->where('rooms.room_type', $q);
        }

        return $query;
    }

    /**
     * Lọc phòng theo khoảng giá dựa theo bảng room
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopePriceDayFrom($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.price_day', '>=', $q);
        }
        return $query;
    }

    public function scopePriceDayTo($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.price_day', '<=', $q);
        }
        return $query;
    }

    public function scopePriceHourFrom($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.price_hour', '>=', $q);
        }
        return $query;
    }

    public function scopePriceHourTo($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.price_hour', '<=', $q);
        }
        return $query;
    }

    /**
     *
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
            $query->where('rooms.new', $q);
        }
        return $query;
    }

    /**
     *
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
            $query->where('rooms.hot', $q);
        }
        return $query;
    }

    /**
     * Lấy ra phòng có nhiều booking nhất
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeMostPopular($query, $q)
    {
        return $query->orderBy('rooms.total_booking', 'desc');
    }

    /**
     * Số giường
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeNumberBed($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.number_bed', '>=', $q);
        }

        return $query;
    }

    /**
     * Số khách
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeNumberGuest($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.max_guest', '>=', $q);
        }

        return $query;
    }

    /**
     * Lọc giá từ thấp đến cao hoặc ngược lại
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeSortPriceDay($query, $q)
    {
        $sort = 'asc';
        if (is_numeric($q) && $q == 1) {
            $sort = 'desc';
        }

        return $query->where('rooms.price_day', '>', 0)->orderBy('rooms.price_day', $sort);
    }

    /**
     * Sắp xếp phòng theo tổng số lượng reviews
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeSortTotalReview($query, $q)
    {
        $sort = 'asc';
        if (is_numeric($q) && $q == 1) {
            $sort = 'desc';
        }

        return $query->where('rooms.total_review', '>', 0)->orderBy('rooms.total_review', $sort);
    }

    /**
     * Sắp xếp phòng theo tổng số lượng recommend
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeSortTotalRecommend($query, $q)
    {
        $sort = 'asc';
        if (is_numeric($q) && $q == 1) {
            $sort = 'desc';
        }

        return $query->where('rooms.total_recommend', '>', 0)->orderBy('rooms.total_recommend', $sort);
    }

    /**
     * Theo danh sách comforts
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeComfortLists($query, $q)
    {
        $arr = explode(',', $q);
        if (!empty($q)) {
            // Kiểm tra các phần tử trong mảng phải là số
            foreach ($arr as $item) {
                if (!is_numeric($item)) {
                    return $query;
                }
            }

            if (!self::isJoined($query, 'room_comforts')) {
                $query->join('room_comforts', 'rooms.id', '=', 'room_comforts.room_id')->select('room.*');
            }


            $query = $query->whereIn('room_comforts.comfort_id', $arr)
                           ->groupBy('rooms.id')
                           ->havingRaw('count(*) = ?', [count($arr)]);
        }
        return $query;
    }

    /**
     * Độ sạch sẽ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeCleanliness($query, $q)
    {

        // if (!self::isJoined($query, 'room_reviews')) {
        //     $query->join('room_reviews', 'rooms.id', '=', 'room_reviews.room_id')->select('rooms.*')->select('rooms.*');
        // }

        if (is_numeric($q)) {
            $query->where('rooms.avg_cleanliness', '>=', $q);
        }
        
        return $query;
    }

    /**
     * Dịch vụ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeService($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.avg_service', '>=', $q);
        }

        return $query;
    }

    /**
     * Giá trị
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeValuable($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.avg_valuable', '>=', $q);
        }
        return $query;
    }

    /**
     * Chất lượng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeQuality($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.avg_quality', '>=', $q);
        }
        return $query;
    }

    /**
     * Scope theo avg_rating
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopeAvgRating($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('rooms.avg_avg_rating', '>=', $q);
        }
        return $query;
    }
    
    /**
     * Scope theo standard_point : = 5 hoặc <= 4
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopeStandardPoint($query, $q)
    {
        if (is_numeric($q)) {
            if ($q == 5) {
                $query->where('rooms.standard_point', '=', $q);
            } else {
                $query->where('rooms.standard_point', '<=', $q);
            }
        }
        return $query;
    }
}
