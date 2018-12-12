<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{

    /**
     * RoomRepository constructor.
     *
     * @param Room $room
     */
    public function __construct(
        Room $room
    ) {
        $this->model = $room;
    }



    /**
     * Lấy tất cả phòng trừ các phòng có ID trong danh sách
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $list
     * @param array $params
     * @param       $size
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getAllRoomExceptListId(array $list, $params, $size)
    {
        $this->useScope($params, ['check_in', 'check_out']);
        return $this->model
            ->whereNotIn('rooms.id', $list)
            ->where('rooms.status', Room::AVAILABLE)
            ->orderBy('is_manager', 'desc')
            ->orderBy('avg_avg_rating', 'desc')
            ->orderBy('total_review', 'desc')
            ->orderBy('total_recommend', 'desc')
            ->paginate($size);
    }

    public function getRoom($id)
    {
        return $this->model
            ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
            ->where('room_translates.lang', 'vi')
            ->where('rooms.id', $id)->first();
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $refund
     * @return false|string
     * @throws \Exception
     */
    public function checkValidRefund($data)
    {
        //  Nếu không tích chọn 2 trường hợp: có hủy và không cho hủy thì mặc định là không cho hủy phòng
        if (empty($data)) {
            $refund = [
                'no_booking_cancel' => BookingConstant::BOOKING_CANCEL_UNAVAILABLE,
            ];

            return json_encode($refund);
        }

        if (isset($data['no_booking_cancel'])) {
            if (!empty($data['no_booking_cancel']) && $data['no_booking_cancel'] == BookingConstant::BOOKING_CANCEL_UNAVAILABLE) {
                $refund = [
                    'no_booking_cancel' => BookingConstant::BOOKING_CANCEL_UNAVAILABLE,
                ];

                return json_encode($refund);
            }
        }

        // set măc định 1 mức cho hủy phòng.
        $refund = $data['refunds'];
        if (isset($refund[BookingConstant::BOOKING_CANCEL_lEVEL])) {
            throw new \Exception('không được phép tạo thêm mức hủy phòng');
        }


        $refunds = [
            'refunds'            => $refund,
            'no_booking_cancel' => BookingConstant::BOOKING_CANCEL_AVAILABLE,
        ];

        return json_encode($refunds);
    }

    public function getRoomLatLong($data, $size)
    {
        $room = $this->model
            ->where('longitude', '>', $data["long_min"])
            ->where('longitude', '<', $data["long_max"])
            ->where('latitude', '>', $data["lat_min"])
            ->where('latitude', '<', $data["lat_max"])
            ->paginate($size);

        return $room;
    }

    public function getRoomRecommend($size, $id)
    {
        $room = $this->model->find($id);

        $rooms = $this->model
            ->where('city_id', $room->city_id)
            ->where('district_id', $room->district_id)
            ->where('max_guest', '>=', $room->max_guest)
            ->where('status', Room::AVAILABLE);

        if ($room->standard_point != null) {
            $rooms->where('standard_point', '>=', $room->standard_point);
        }

        if ($room->rent_type != null) {
            $rooms->whereIn('rent_type', [$room->rent_type, Room::TYPE_ALL]);
        }

        $rooms->orderBy('is_manager', 'desc')
            ->orderBy('avg_avg_rating', 'desc')
            ->orderBy('total_review', 'desc')
            ->orderBy('total_recommend', 'desc');

        if($size == -1){
            return $rooms->get();
        }

        return $rooms->paginate($size);
    }
}
