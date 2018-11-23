<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use App\Repositories\Traits\Scope;

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
     * Cập nhật riêng lẻ các thuộc tính của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $id
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function minorRoomUpdate($id, $data = [])
    {
        return parent::update($id, $data);
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
            ->paginate($size);
    }

    public function getRoom($id)
    {
       return $this->model
            ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
            ->where('room_translates.lang','vi')
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
    public function checkVaildRefund($data)
    {
        dd(Room::BOOKING_CACEL_lEVEL -1);
        //  Nếu không tích chọn 2 trường hợp: có hủy và không cho hủy thì mặc định là không cho hủy phòng
        if (empty($data))
        {
            return $data['no_booking_cacel'] =0;
        }



        if (isset($data['no_booking_cacel']) )
        {
           if (empty($data['no_booking_cacel']))
           {
              return $data['no_booking_cacel'] =0;

           }
           return $data['no_booking_cacel'];
        }



        // set măc định bốn mức hủy phòng
            $refund = $data['refunds'];
        if (isset($refund[Room::BOOKING_CACEL_lEVEL])) throw new \Exception('không được phép tạo thêm mức hủy phòng');
       for ($i = 0; $i < Room::BOOKING_CACEL_lEVEL -1 ;$i++ )
       {
           if (!empty($refund[$i+1])){
              if ($refund[$i]['amount'] > $refund[$i+1]['amount'] && $refund[$i]['days'] < $refund[$i+1]['days'] )
              {
                  throw new \Exception('Ngày điền không hợp lệ');
              }elseif ( $refund[$i]['amount'] < $refund[$i+1]['amount'] && $refund[$i]['days'] > $refund[$i+1]['days'] )
              {
                  throw new \Exception('Ngày điền không hợp lệ');
              }
           }
       }

        // kiểm tra cùng một số tiền hoàn lại không thể trùng số ngày với nhau
        $refund_map = array_map(function ($item) {
            return $item['days'];
        },$refund);

        $refund_uique = array_unique($refund_map);
        if(count($refund_map) > count($refund_uique)) throw new \Exception('Số ngày ở các nức hoàn tiền không thể giống nhau');
        return  json_encode($refund);
    }
}
