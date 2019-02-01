<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Cities\City;
use App\Repositories\Search\SearchConstant;
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
    public function getAllRoomExceptListId(array $list, $params, $size, $count = null)
    {
        $alias = $this->model->transformerAlias();
        $this->useScope($params, ['check_in', 'check_out']);
        $this->eagerLoadWithTransformer($params, $alias);

        $query  = $this->model
                ->whereNotIn('rooms.id', $list)
                ->where('rooms.status', Room::AVAILABLE)
                ->where('rooms.merchant_status', Room::AVAILABLE)
                ->orderBy('is_manager', 'desc')
                ->orderBy('avg_avg_rating', 'desc')
                ->orderBy('total_review', 'desc')
                ->orderBy('total_recommend', 'desc');
        if (is_null($count)) {
            return $query->paginate($size);
        } elseif ($count ==='comfort_lists') {
            return $query
                ->join('room_comforts', 'rooms.id', '=', 'room_comforts.room_id')
                ->join('comfort_translates', 'room_comforts.comfort_id', '=', 'comfort_translates.comfort_id')
                ->select(
                    DB::Raw('comfort_translates.name as name_comfort'),
                    DB::Raw('comfort_translates.comfort_id'),
                    DB::Raw('comfort_translates.comfort_id, COUNT(*) as total_rooms')
                )
                ->groupBy('room_comforts.comfort_id')
                -> orderBy('total_rooms', 'desc')
                ->get()->toArray();
        } elseif ($count === 'standard_point') {
            return $query
                ->select(DB::Raw('rooms.standard_point, COUNT(*) as count'))
                ->groupBy('rooms.standard_point')->orderBy('standard_point')->get()->toArray();
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

    public function getRoomRecommend($size = 10, $id)
    {
        // Thành phố, quận huyện, đặt phòng nhanh, giá tương tự, số khách, hot, total_booking, total_reviews
        $room = $this->model->find($id);
        $rooms = $this->model->where(
                [
                    ['city_id', $room->city_id],
                    ['district_id', $room->district_id],
                    ['max_guest','>=', $room->max_guest],
                    ['status', $room->status]
                ]
            );
            
        // if ($room->standard_point != null) {
        //     $rooms->where('standard_point', '>=', $room->standard_point);
        // }
                
        if ($room->rent_type != null) {
            $rooms = $rooms->whereIn('rent_type', [$room->rent_type, Room::TYPE_ALL])->whereNotIn('id', [$id]);
            $condition_price = $room->rent_type == Room::TYPE_HOUR ? 'price_hour' : 'price_day';
            $rooms = $rooms->whereBetween($condition_price, [$room->$condition_price - Room::PRICE_RANGE_RECOMMEND  ,$room->$condition_price + Room::PRICE_RANGE_RECOMMEND]);
        }

        $rooms->orderBy('is_manager', 'desc')
              ->orderBy('total_booking', 'desc')
              ->orderBy('avg_avg_rating', 'desc')
              ->orderBy('total_recommend', 'desc')
              ->orderBy('total_review', 'desc');

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
    public function countRoomByType($date_start, $date_end, $view, $status = Room::AVAILABLE)
    {
        $query = $this->model
            ->select(
                DB::raw('rooms.room_type as room_type'),
                DB::raw('count(rooms.room_type) as total_room')
                // DB::raw('cast(rooms.created_at as DATE) as createdAt')
            );
        if ($status !== null) {
            $query = $query->where('status', $status);
        }

        // ->groupBy(DB::raw('createdAt,rooms.room_type'))->get();
        $rooms = $query->groupBy(DB::raw('rooms.room_type'))->get();

        $data_date       = [];
        $convertDataRoom = [];
        foreach ($rooms as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountRoomType = $this->convertCountRoomType($rooms, $val);

            $convertDataRoom[] = $convertCountRoomType;
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
        // dd($dataBooking);
        foreach ($dataBooking as $key => $value) {
            $convertRoom[] = [
                'name'       => Room::ROOM_TYPE[$value['room_type']],
                'room_type'  => $value->room_type,
                'y'          => $value->total_room,
            ];
        }
        return $convertRoom;
    }

    /**
     * đếm room theo quận huyện
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
                DB::raw('count(rooms.id) as total_room')
                // DB::raw('cast(rooms.created_at as DATE) as createdAt')
            )
            ->where([
                // ['rooms.created_at', '>=', $date_start],
                // ['rooms.created_at', '<=', $date_end],
                ['rooms.status', '=', Room::AVAILABLE],
                ['rooms.merchant_status', '=', Room::AVAILABLE],
            ])

            // ->groupBy(DB::raw('createdAt,districts.name'))->get();
            ->groupBy(DB::raw('districts.name'))->get();

        $data_date       = [];
        $convertDataRoom = [];
        foreach ($rooms as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountRoomDistrict = $this->convertCountRoomDistrict($rooms, $val);

            $convertDataRoom[] = $convertCountRoomDistrict;
        }

        return $convertDataRoom;
    }

    /**
     * lấy những room có cùng ngày theo thành phố
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
    public function countRoomByTopBooking($lang, $take, $sort, $date_start, $date_end)
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
                ['rooms.merchant_status', '=', Room::AVAILABLE],
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end]
            ])
            ->groupBy(DB::raw('total_booking'))
            ->orderBy('total_booking', $sort)
            ->take($take)
            ->get();
        $room_top_bookings = [];
        foreach ($rooms as $k => $v) {
            $room_top_bookings[] = [
                'name' => $v->name,
                'data' => [$v->total_booking]
            ];
        }
        return $room_top_bookings;
    }

    /**
     * Cập nhật commission cho toàn bộ phòng
     * @author ducchien0612 <ducchien0612@gmail.com>
     * @
     */
    public function updateComission($data)
    {
        return $this->model->where('status', Room::AVAILABLE)->update($data);
    }

    /**
     * đếm room theo quận huyện
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function countRoomByCity($date_start, $date_end, $view, $status = Room::AVAILABLE)
    {
        $query = $this->model
            ->join('cities', 'cities.id', '=', 'rooms.city_id')
            ->select(
                DB::raw('cities.name as name_city'),
                DB::raw('count(rooms.id) as total_room')
                // DB::raw('cast(rooms.created_at as DATE) as createdAt')
            );
            
        if ($status !== null) {
            $query = $query->where('status', $status);
        }

        // ->groupBy(DB::raw('createdAt,cities.name'))->get();
        $rooms = $query->groupBy(DB::raw('cities.name'))->get();

        $data_date       = [];
        $convertDataRoom = [];
        foreach ($rooms as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountRoomCity = $this->convertCountRoomCity($rooms, $val);

            $convertDataRoom[] = $convertCountRoomCity;
        }

        return $convertDataRoom;
    }

    /**
     * lấy những room có cùng ngày theo thành phố
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountRoomCity($rooms, $createdAt)
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
                'name' => $value->name_city,
                'data' => [$value->total_room],
            ];
        }
        return $convertRoom;
    }

    /**
     * Lấy ra danh sách các tên phòng theo từ khóa khi không đử 6 gợi ý từ thành phố , quận huyện
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getRoomNameUserForSearch($data, $request, $count)
    {
        $result_room_name = $this->model
             ->select('room_translates.name', 'rooms.hot', 'rooms.status', 'room_translates.address', 'rooms.id', 'rooms.room_type')
             ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
             ->where('room_translates.name', 'like', "%$request->key%")
             ->orWhere(\DB::raw("REPLACE(room_translates.name, ' ', '')"), 'LIKE', '%' . $request->key. '%')
             ->where('rooms.status', Room::AVAILABLE)
             ->where('rooms.merchant_status', Room::AVAILABLE)
             ->orderBy('rooms.hot', 'DESC')
             ->limit(SearchConstant::SEARCH_SUGGESTIONS-$count)->get()->toArray();

        $result_room_name = array_map(function ($item) {
            return [
                'id'                => $item['id'],
                'name'              => $item['name'],
                'hot'               => $item['hot'],
                'hot_txt'           => ($item['hot'] == 1) ? 'Phổ biến' : null,
                'room_type_text'    => Room::ROOM_TYPE[$item['room_type']],
                'type'              => SearchConstant::ROOM_NAME,
                'description'       => SearchConstant::SEARCH_TYPE[SearchConstant::ROOM_NAME],
            ];
        }, $result_room_name);

        $result =  array_merge($data, $result_room_name);

        $count = collect($result)->count();

        return $list = [$count,$result];
    }

    /**
     * Đếm số lượng phòng theo thành phố
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function countNumberOfRoomByCity($params = [], $limit = 10)
    {
        // $this->useScope($params);
        $rooms = $this->model
                ->select(
                    DB::raw('rooms.city_id'),
                    DB::raw('cities.name as name_city'),
                    DB::raw('round(avg(rooms.price_day)) as average_price'),
                    DB::raw('cities.image'),
                    DB::raw('count(rooms.city_id) as total_rooms')
                );
        if (isset($params['hot'])) {
            $rooms->where('cities.hot', $params['hot']);
        }
        $rooms = $rooms->join('cities', 'cities.id', '=', 'rooms.city_id')
                ->groupBy(DB::raw('rooms.city_id'))
                ->orderBy('total_rooms', 'desc');

        if ($limit == -1) {
            return $rooms->get();
        }
        // dd($rooms->toSql());
        // dd($rooms->get(10));
        return $rooms->take($limit)->get();
    }

    /**
     * đếm số phòng theo loại phòng, thời gian
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function countRoomByTypeCompare($date_start, $date_end, $view, $status = Room::AVAILABLE)
    {
        $selectRawView = $this->switchViewRoomCreatedAt($view);

        $query = $this->model
            ->select(
                DB::raw('rooms.room_type as room_type'),
                DB::raw('count(rooms.id) as total_room'),
                DB::raw($selectRawView)
            )
            ->where([
                ['rooms.created_at', '>=', $date_start],
                ['rooms.created_at', '<=', $date_end],
            ]);
        if ($status !== null) {
            $query = $query->where('status', $status);
        }

        $rooms = $query->groupBy(DB::raw('createdAt, room_type'))->get();

        $data_date       = [];
        $convertDataRoom = [];
        foreach ($rooms as $key => $value) {
            array_push($data_date, $value->createdAt);
        }
        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            // dd($rooms);
            $convertCountRoomType = $this->convertCountRoomType($rooms, $val);

            $convertDataRoom[] = [
                $val => $convertCountRoomType
            ];
        }

        return $convertDataRoom;
    }

    /**
     * Lấy ra dữ liệu phòng phục vụ cho host reviews
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function getRoomForReview($id)
    {
        return $this->model->select('rooms.id', 'room_translates.name', 'room_medias.image')
                    ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
                    ->join('room_medias', 'rooms.id', '=', 'room_medias.room_id')
                    ->where('rooms.id', '=', $id)
                    ->where('room_translates.lang', '=', 'vi')
                    ->first();
    }

    /**
     * Đếm số lượng phòng theo quận huyện
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function countNumberOfRoomByDistrict($params = [], $limit = 10)
    {
        // $this->useScope($params);
        $rooms = $this->model
                ->select(
                    DB::raw('rooms.district_id'),
                    DB::raw('districts.name as name_district'),
                    DB::raw('districts.image'),
                    DB::raw('count(rooms.district_id) as total_rooms')
                );
        if (isset($params['hot'])) {
            $rooms->where('districts.hot', $params['hot']);
        }
        $rooms = $rooms->join('districts', 'districts.id', '=', 'rooms.district_id')
                ->groupBy(DB::raw('rooms.district_id'))
                ->orderBy('total_rooms', 'desc');

        if ($limit == -1) {
            return $rooms->get();
        }
        // dd($rooms->get(10));
        return $rooms->take($limit)->get();
    }
}
