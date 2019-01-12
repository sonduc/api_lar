<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;
use Illuminate\Support\Facades\DB;

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
    public function getAllRoomExceptListId(array $list, $params, $size,$count = null)
    {
        $alias = $this->model->transformerAlias();
        $this->useScope($params, ['check_in', 'check_out']);
        $this->eagerLoadWithTransformer($params, $alias);

        $query  = $this->model
                ->whereNotIn('rooms.id', $list)
                ->where('rooms.status', Room::AVAILABLE)
                ->orderBy('is_manager', 'desc')
                ->orderBy('avg_avg_rating', 'desc')
                ->orderBy('total_review', 'desc')
                ->orderBy('total_recommend', 'desc');
        if (is_null($count))
        {
            return $query->paginate($size);
        }elseif ($count ==='comfort_lists')
        {
           return $query
                ->join('room_comforts', 'rooms.id', '=', 'room_comforts.room_id')->select('room.*')
                ->select(DB::Raw('room_comforts.comfort_id, COUNT(*) as count'))
                ->groupBy('room_comforts.comfort_id')
                -> orderBy('comfort_id')
                ->get();

        }elseif ($count === 'standard_point')
        {
                 return $query
                ->select(DB::Raw('rooms.standard_point, COUNT(*) as count'))
                ->groupBy('rooms.standard_point')->orderBy('standard_point')->get();

        }

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
     *
     * @return false|string
     * @throws \Exception
     */
    public function checkValidRefund($data)
    {
        //  Nếu không tích chọn 2 trường hợp: có hủy và không cho hủy thì mặc định là không cho hủy phòng
        if (empty($data['settings']) || !isset($data['settings'])) {
            $refund = [['days' => 14, 'amount' => 100]];
            $refunds = [
                'refunds'           => $refund,
                'no_booking_cancel' => BookingConstant::BOOKING_CANCEL_AVAILABLE,
            ];
            return json_encode($refunds);
        }

        if (isset($data['settings']['no_booking_cancel'])) {
            if (!empty($data['settings']['no_booking_cancel']) && $data['settings']['no_booking_cancel'] == 1) {
                $refund = [
                    'no_booking_cancel' => BookingConstant::BOOKING_CANCEL_UNAVAILABLE,
                ];

                return json_encode($refund);
            }
        }
        // set măc định 1 mức cho hủy phòng.
        $refund = $data['settings']['refunds'];
        if (isset($refund[BookingConstant::BOOKING_CANCEL_lEVEL])) {
            throw new \Exception('không được phép tạo thêm mức hủy phòng');
        }

        $refunds = [
            'refunds'           => $refund,
            'no_booking_cancel' => BookingConstant::BOOKING_CANCEL_AVAILABLE,
        ];

        return json_encode($refunds);
    }

    public function getRoomLatLong($params = [], $size = 25, $trash = self::NO_TRASH)
    {
        $sort = array_get($params, 'sort', 'created_at:-1');
        $params['sort'] = $sort;
        $this->useScope($params);
        
        $room           = $this->model
            ->where('longitude', '>=', floatval($params["long_min"]))
            ->where('longitude', '<=', floatval($params["long_max"]))
            ->where('latitude', '>=', floatval($params["lat_min"]))
            ->where('latitude', '<=', floatval($params["lat_max"]));
        switch ($trash) {
            case self::WITH_TRASH:
                $room->withTrashed();
                break;
            case self::ONLY_TRASH:
                $room->onlyTrashed();
                break;
            case self::NO_TRASH:
            default:
                break;
        }

        switch ($size) {
            case -1:
                return $room->get();
                break;
            case 0:
                return $room->first();
            default:
                return $room->paginate($size);
                break;
        }
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

        if ($size == -1) {
            return $rooms->get();
        }

        return $rooms->paginate($size);
    }

    public function getListCalendar($list_id)
    {
        $airbnb_calendar = $this->model::whereIn('id', $list_id)->pluck('airbnb_calendar', 'id');
        return $airbnb_calendar;
    }

    public function getRoomByMerchantId($id, $params, $size)
    {
        $this->useScope($params);
        return $this->model
            ->where('rooms.merchant_id', $id)
            ->paginate($size);
    }

    /**
     *  // Tính số phần trăm hoàn thành
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     *
     * @return float
     */

    public function calculation_percent($data)
    {
        // Tính số phần trăm hoàn thành
        $data   = array_only($data, ['weekday_price', 'room_time_blocks', 'optional_prices', 'basic', 'details', 'comforts', 'images', 'prices', 'settings']);
        $except = ['weekday_price', 'room_time_blocks', 'optional_prices'];
        empty($data['comforts']) ? array_push($except, 'comforts') : null;
        empty($data['images']) ? array_push($except, 'images') : null;

        $count   = array_except($data, $except);
        $percent = count($count) / Room::FINISHED * 100;
        return round($percent);
    }

    /**
     * Get Comission rate of the room
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param $data
     * @return float
     */
    public function getRoomComission($id)
    {
        return $this->model->where('id', $id)->pluck('comission')->first();
    }
    
    /**
     * Xử lí dữ liệu ngày, tháng, năm để thống kê booking theo trường create_at
     * @param  [type] $view [description]
     * @return [type]       [description]
     */
    public function switchViewRoomCreatedAt($view)
    {
        switch ($view) {
            case 'day':
                $selectRawView = 'cast(rooms.created_at as DATE) as createdAt';
                break;
            case 'month':
                $selectRawView = 'DATE_FORMAT(DATE_ADD(rooms.created_at,INTERVAL (1 - DAYOFMONTH(rooms.created_at)) DAY),"%m-%Y") AS createdAt';
                break;
            case 'year':
                $selectRawView = 'DATE_FORMAT(DATE_ADD(rooms.created_at,INTERVAL (1 - DAYOFMONTH(rooms.created_at)) DAY),"%Y") AS createdAt';
                break;
            default:
                $selectRawView = 'CONCAT(CAST(DATE_ADD(rooms.created_at,INTERVAL (1 - DAYOFWEEK(rooms.created_at)) DAY) AS DATE)," - ",CAST(DATE_ADD(rooms.created_at,INTERVAL (7 - DAYOFWEEK(rooms.created_at)) DAY) AS DATE)) AS createdAt';
                break;
        }
        return $selectRawView;
    }

    /**
     * đếm room theo loại phòng
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function countRoomByType($date_start, $date_end, $view, $status)
    {
        $rooms = $this->model
            ->select(
                DB::raw('rooms.room_type as room_type'),
                DB::raw('count(rooms.room_type) as total_room'),
                DB::raw('cast(rooms.created_at as DATE) as createdAt')
            )
            ->where([
                ['rooms.created_at', '>=', $date_start],
                ['rooms.created_at', '<=', $date_end],
                ['rooms.status', '=', Room::AVAILABLE],
            ])

            ->groupBy(DB::raw('createdAt,rooms.room_type'))->get();

        $data_date       = [];
        $convertDataRoom = [];
        foreach ($rooms as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountRoomType = $this->convertCountRoomType($rooms, $val);

            $convertDataRoom[] = [
                'createdAt' => $val,
                'data'      => $convertCountRoomType,
            ];
        }

        return $convertDataRoom;
    }

    /**
     * lấy những room có cùng ngày theo loại phòng
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountRoomType($rooms, $createdAt)
    {
        $dataRoom    = [];
        $convertRoom = [];
        foreach ($rooms as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertRoom[] = [
                'room_type_txt' => Room::ROOM_TYPE[$value['room_type']],
                'room_type'     => $value->room_type,
                'total_room'    => $value->total_room,
            ];
        }
        return $convertRoom;
    }

    /**
     * đếm room theo tỉnh thành
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function countRoomByDistrict($date_start, $date_end, $view, $status)
    {
        $rooms = $this->model
            ->join('districts', 'districts.id', '=', 'rooms.district_id')
            ->select(
                DB::raw('districts.name as name_district'),
                DB::raw('count(rooms.id) as total_room'),
                DB::raw('cast(rooms.created_at as DATE) as createdAt')
            )
            ->where([
                ['rooms.created_at', '>=', $date_start],
                ['rooms.created_at', '<=', $date_end],
                ['rooms.status', '=', Room::AVAILABLE],
            ])

            ->groupBy(DB::raw('createdAt,districts.name'))->get();

        $data_date       = [];
        $convertDataRoom = [];
        foreach ($rooms as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountRoomDistrict = $this->convertCountRoomDistrict($rooms, $val);

            $convertDataRoom[] = [
                'createdAt' => $val,
                'data'      => $convertCountRoomDistrict,
            ];
        }

        return $convertDataRoom;
    }

    /**
     * lấy những room có cùng ngày theo tỉnh thành
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountRoomDistrict($rooms, $createdAt)
    {
        $dataRoom    = [];
        $convertRoom = [];
        foreach ($rooms as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertRoom[] = [
                'name_district' => $value->name_district,
                'total_room'    => $value->total_room,
            ];
        }
        return $convertRoom;
    }

    /**
     * đếm room có booking nhiều nhất
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countRoomByTopBooking($lang, $take, $sort)
    {
        $rooms = $this->model
            ->join('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
            ->select(
                DB::raw('room_translates.name,count(bookings.id) as total_booking')
            )
            ->where([
                ['room_translates.lang', '=', $lang],
                ['rooms.status', '=', Room::AVAILABLE],
            ])
            ->groupBy(DB::raw('total_booking'))
            ->orderBy('total_booking', $sort)
            ->take($take)
            ->get();
        return $rooms;
    }

    /**
     * đếm room có booking nhiều nhất theo loại phòng
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countRoomByTypeTopBooking($lang, $take, $sort)
    {
        $rooms = $this->model
            ->join('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->select(
                DB::raw('rooms.room_type as room_type'),
                DB::raw('count(bookings.id) as total_booking')
            )
            ->where([
                ['rooms.status', '=', Room::AVAILABLE],
            ])
            ->groupBy(DB::raw('rooms.room_type'))
            ->orderBy('total_booking', $sort)
            ->take($take)
            ->get();

        $arrayConvert =[];
        foreach ($rooms as $key => $value) {
            $arrayConvert[] = [
                'room_type_txt' => Room::ROOM_TYPE[$value->room_type],
                'room_type'     => $value->room_type,
                'total_booking'  => $value->total_booking,
            ];
        }
        return $arrayConvert;
    }
    
    public function updateComission($data)
    {
        return $this->model->where('status', Room::AVAILABLE)->update($data);
    }
}
