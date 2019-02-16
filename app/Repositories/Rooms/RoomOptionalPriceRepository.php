<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomOptionalPriceRepository extends BaseRepository implements RoomOptionalPriceRepositoryInterface
{
    /**
     * @var RoomOptionalPrice
     */
    protected $model;

    /**
     * RoomOptionalPriceRepository constructor.
     *
     * @param RoomOptionalPrice $room
     */
    public function __construct(RoomOptionalPrice $room)
    {
        $this->model = $room;
    }

    /**
     * Cập nhật giá cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     */
    public function updateRoomOptionalPrice($room, $data = [])
    {
        $this->deleteRoomOptionalPriceByRoomID($room);
        $this->storeRoomOptionalPrice($room, $data);
    }

    /**
     * Xóa giá của phòng theo room_id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     */
    public function deleteRoomOptionalPriceByRoomID($room)
    {
        $this->model->where('room_id', $room->id)->forceDelete();
    }

    /**
     * Lưu giá cụ thể cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomOptionalPrice($room, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['weekday_price'])) {
                $roomWeekPrices = $this->storeRoomOptionalWeekdayPrice($room, $data);
                $list           = array_merge($list, $roomWeekPrices);
            }

            if (isset($data['optional_prices']['days'])) {
                $roomDayPrices = $this->storeRoomOptionalDayPrice($room, $data);
                $list          = array_merge($list, $roomDayPrices);
            }
        }
        
        parent::storeArray($list);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     *
     * @return array
     */
    public function storeRoomOptionalWeekdayPrice($room, $data = []): array
    {
        return array_map(function ($item) use ($room) {
            $item['room_id'] = $room->id;
            return $item;
        }, $data['weekday_price']);
    }

    /**
     * Thêm giá theo từng ngày cụ thể cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     *
     * @return array
     */
    public function storeRoomOptionalDayPrice($room, $data = [], $list = [])
    {
        
        // dd($data);
        $price_day        =
            array_key_exists(
                'price_day',
                $data['optional_prices']
            ) ? $data['optional_prices']['price_day'] : 0;
        $price_hour       =
            array_key_exists(
                'price_hour',
                $data['optional_prices']
            ) ? $data['optional_prices']['price_hour'] : 0;
        $price_after_hour =
            array_key_exists(
                'price_after_hour',
                $data['optional_prices']
            ) ? $data['optional_prices']['price_after_hour'] : 0;

        foreach ($data['optional_prices']['days'] as $day) {
            $obj                     = $data;
            $obj['room_id']          = $room->id;
            $obj['day']              = $day;
            $obj['price_day']        = $price_day;
            $obj['price_hour']       = $price_hour;
            $obj['price_after_hour'] = $price_after_hour;
            $obj                     = array_except($obj, 'option');
            $obj                     = array_except($obj, 'optional_prices');
            $list[]                  = $obj;
        }
        // dd($list);

        return $list;
    }

    /**
     * Lấy giá phòng tùy chọn theo mã phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function getOptionalPriceByRoomId($id)
    {
        return $this->model->where('room_id', $id)->get();
    }

    /**
     * Lấy giá theo ngày
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $day
     *
     * @return array
     */
    public function getPriceByDay($id, $day)
    {
        $room = $this->model->where('room_id', $id)->where('status', Room::AVAILABLE)->get();

        foreach ($room as $key => $value) {
            if ($value->weekday === $day['weekday'] || $value->day === $day['day']) {
                return $value;
            }
        }
        return [];
    }

    public function updateSeparateOptionalPrice($room, $data = [])
    {
        // dd(isset($data['option']));
        if (isset($data['option'])) {
            if ($data['option'] === 'optional_prices') {
                $optionalPrices = $this->storeRoomOptionalDayPrice($room, $data);
                foreach ($optionalPrices as $optional_price) {
                    return $this->model->updateOrCreate(['day' => $optional_price['day']], $optional_price);
                }
            } elseif ($data['option'] === 'weekday_price') {
                $optionalPrices = $this->storeRoomOptionalWeekdayPrice($room, $data);
                foreach ($optionalPrices as $optional_price) {
                    return $this->model->updateOrCreate(['weekday' => $optional_price['weekday']], $optional_price);
                }
            }
            // dd($optionalPrices);
        }
        if (!isset($data['option'])  || (isset($data['option']) && $data['option'] === 'all')) {
            return $this->updateRoomOptionalPrice($room, $data);
        }
    }
}
