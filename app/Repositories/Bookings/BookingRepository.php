<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Rooms\Room;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use DB;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    /**
     * BookingRepository constructor.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->model = $booking;
    }

    /**
     * Lấy các booking chưa bị hủy và có thời gian checkout lớn hơn hiện tại
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function getFutureBookingByRoomId($id)
    {
        $dateNow = Carbon::now();
        $data    = $this->model->where([
            ['status', '<>', BookingConstant::BOOKING_CANCEL],
            ['checkout', '>', $dateNow->timestamp],
            ['created_at', '>', $dateNow->copy()->addYears(-1)],
            ['room_id', $id],
        ])->get();
        return $data;
    }

    /**
     * Check xem người này đã hoàn thành booking và checkout chưa
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */

    public function getBookingByCheckout($id)
    {
        $dateNow = Carbon::now();
        $data    = $this->model->where([
            ['id', $id],
            ['checkout', '<', $dateNow->timestamp],
            ['status', '=', BookingConstant::BOOKING_COMPLETE],
        ])->first();
        if (empty($data)) {
            throw new \Exception('Bạn chưa hoàn thành booking này nên chưa có quyền đánh giá');
        }
        return $data;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param string $start
     * @param string $end
     *
     * @return Collection
     */
    public function getAllBookingInPeriod($start, $end)
    {
        try {
            list($y_start, $m_start, $d_start) = explode('-', $start);
            list($y_end, $m_end, $d_end)       = explode('-', $end);

            $checkIn  = Carbon::createSafe((int) $y_start, (int) $m_start, (int) $d_start)->endOfDay()->timestamp;
            $checkOut = Carbon::createSafe((int) $y_end, (int) $m_end, (int) $d_end)->endOfDay()->timestamp;
        } catch (InvalidDateException | \ErrorException $exception) {
            $checkIn  = Carbon::now()->addDay()->endOfDay()->timestamp;
            $checkOut = Carbon::now()->addDay()->addMonth()->endOfDay()->timestamp;
        }

        $data = $this->model
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where([
                    ['bookings.checkin', '<', $checkIn],
                    ['bookings.checkout', '>', $checkIn],
                ])->orWhere([
                    ['bookings.checkin', '<', $checkOut],
                    ['bookings.checkout', '>', $checkOut],
                ])->orWhere([
                    ['bookings.checkin', '>', $checkIn],
                    ['bookings.checkout', '<', $checkOut],
                ]);
            })
            ->whereNotIn(
                'bookings.status',
                [BookingConstant::BOOKING_CANCEL, BookingConstant::BOOKING_COMPLETE]
            )->distinct()->select('room_id')->get();

        return $data;
    }

    /**
     * Lấy tất cả các booking theo id người dùng có phân trang
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $size
     *
     * @return mixed
     */

    public function getBookingById($id, $size)
    {
        return $this->model
            ->where('bookings.customer_id', $id)
            ->paginate($size);
    }

    public function updatStatusBooking($booking)
    {
        $data    = $booking->data;
        $uuid    = $booking->data['uuid'];
        $booking = $this->getBookingByUuid($uuid);
        parent::update($booking->id, $data);
    }
    /**
     * Lấy tất cả các bản ghi sắp sắn đến ngày checkin, checkout
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getAllBookingFuture()
    {
        $dateNow            = Carbon::now();
        $dateNow_timestamp  = Carbon::now()->timestamp;
        $tomorrow           = $dateNow->addDay();
        $tomorrow_timestamp = $tomorrow->timestamp;
        $data               = $this->model
            ->where('checkin', '>=', $dateNow_timestamp)
            ->where('checkout', '>=', $dateNow_timestamp)
            ->where('checkin', '<', $tomorrow_timestamp)
            ->whereIn('status', [
                BookingConstant::BOOKING_NEW,
                BookingConstant::BOOKING_CONFIRM,
                BookingConstant::BOOKING_USING,
            ])
            ->get();
        return $data;
    }

    /**
     * Lấy tất cả các bản ghi qua ngày checkout trong khoảng 24h
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getAllBookingCheckoutOneDay()
    {
        $dateNow             = Carbon::now();
        $dateNow_timestamp   = Carbon::now()->timestamp;
        $yesterday_timestamp = $dateNow->subHours(27)->timestamp;
        $data                = $this->model
            ->where('checkout', '<', $dateNow_timestamp)
            ->where('checkout', '>', $yesterday_timestamp)
            ->where('status', BookingConstant::BOOKING_COMPLETE)
            ->get();
        // dd($data);
        return $data;
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $uuid
     * @return mixed
     */
    public function getBookingByUuid($uuid)
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $code
     * @return mixed
     */
    public function getBookingByCode($code)
    {
        return $this->model->where('code', $code)->first();
    }

    /**
     * Lấy tất cả danh sách email khách hàng tạo booking thành công
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getBookingSuccess($params)
    {
        $query = $this->model
            ->select('bookings.name', 'bookings.email')
            ->join('users', 'bookings.customer_id', '=', 'users.id')
            ->where('bookings.status', BookingConstant::BOOKING_COMPLETE)
            ->where('bookings.email', '<>', null)
            ->groupBy('bookings.email');

        if (isset($params['owner']) == true && is_numeric($params['owner'])) {
            $query->where('users.owner', $params['owner']);
            if (isset($params['city']) == true && is_numeric($params['city'])) {
                $query->where('users.city_id', $params['city']);
            }
        }

        return $query->get();
    }

    /**
     * Lấy tất cả danh sách email trong khoảng tháng
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getBookingCheckout($params)
    {
        $the_begin_of_the_year = Carbon::parse('first day of January')->timestamp;
        $today                 = Carbon::now();

        $query = $this->model
            ->select('name', 'email', 'checkout')
            ->where('email', '<>', null)
            ->where('status', BookingConstant::BOOKING_COMPLETE)
            ->groupBy('email');

        if (isset($params['month']) == true && is_numeric($params['month'])) {
            $ojDay = $today->subMonths($params['month'])->timestamp;
        } else {
            $ojDay = $today->subMonths(3)->timestamp;
        }
        $query->where('checkout', '<', $ojDay);
        $query->where('checkout', '', $the_begin_of_the_year);
        return $query->get();
    }

    /**
     * Xử lí dữ liệu ngày, tháng, năm để thống kê booking
     * @param  [type] $view [description]
     * @return [type]       [description]
     */
    public function switchViewBookingCreatedAt($view)
    {
        switch ($view) {
            case 'day':
                $selectRawView = 'cast(bookings.created_at as DATE) as createdAt';
                break;
            case 'month':
                $selectRawView = 'DATE_FORMAT(DATE_ADD(bookings.created_at,INTERVAL (1 - DAYOFMONTH(bookings.created_at)) DAY),"%m-%Y") AS createdAt';
                break;
            case 'year':
                $selectRawView = 'DATE_FORMAT(DATE_ADD(bookings.created_at,INTERVAL (1 - DAYOFMONTH(bookings.created_at)) DAY),"%Y") AS createdAt';
                break;
            default:
                $selectRawView = 'CONCAT(CAST(DATE_ADD(bookings.created_at,INTERVAL (1 - DAYOFWEEK(bookings.created_at)) DAY) AS DATE)," - ",CAST(DATE_ADD(bookings.created_at,INTERVAL (7 - DAYOFWEEK(bookings.created_at)) DAY) AS DATE)) AS createdAt';
                break;
        }
        return $selectRawView;
    }
    /**
     * đếm booking theo trạng thái
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByStatus($date_start, $date_end, $view, $status)
    {   
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $booking = $this->model
            ->select(
                DB::raw('count(id) as total_booking'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )

            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
            if($status != null ) {
                $booking->where('bookings.status',$status);
            }
            
        return $booking->groupBy(DB::raw('createdAt'))->get();
    }

    /**
     * đếm booking theo tỉnh thành
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByCity($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('cities. NAME as name_city'),
                DB::raw('count(cities.name) as total_booking'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->join('cities', 'cities.id', '=', 'rooms.city_id')
            ->whereRaw('bookings.room_id = rooms.id')
            ->whereRaw('rooms.city_id = cities.id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        if($status != null ) {
            $bookings->where('bookings.status',$status);
        }
        $bookings = $bookings->groupBy(DB::raw('createdAt,name_city'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCityBooking = $this->convertCityBooking($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data' => $convertCityBooking,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo thành phố
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCityBooking($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'name_city'     => $value->name_city,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking theo tỉnh thành
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByDistrict($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('districts. NAME as name_district'),
                DB::raw('count(districts.name) as total_booking'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->join('districts', 'districts.id', '=', 'rooms.district_id')
            ->whereRaw('bookings.room_id = rooms.id')
            ->whereRaw('rooms.district_id = districts.id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);

        if($status != null ) {
            $bookings->where('bookings.status',$status);
        }

        $bookings = $bookings->groupBy(DB::raw('createdAt,name_district'))->get();    
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertDistrictBooking = $this->convertDistrictBooking($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data' => $convertDistrictBooking,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo tỉnh thành
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertDistrictBooking($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'name_district' => $value->name_district,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking theo type
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByType($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('count(id) as total_booking,type'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);

        if($status != null ) {
            $bookings->where('bookings.status',$status);
        }
        
        $bookings = $bookings->groupBy(DB::raw('createdAt,type'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertBookingType = $this->convertBookingType($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data' => $convertBookingType,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại booking
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertBookingType($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'type_txt'      => BookingConstant::BOOKING_TYPE[$value->type],
                'type'          => $value->type,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }
        return $convertBooking;
    }

    /**
     * tính tổng tiền của booking theo trạng thái thanh toán và trạng thái booking
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function totalBookingByRevenue($date_start, $date_end, $view)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' and `payment_status` = ' . BookingConstant::PAID . ' then total_fee else 0 end) as revenue'),
                DB::raw('sum(case when `payment_status` = ' . BookingConstant::PAID . ' then total_fee else 0 end) as total_revenue'),
                DB::raw($selectRawView)
            )
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('createdAt'))
            ->get();

        return $bookings;
    }

    /**
     * tính tổng tiền của booking theo trạng thái thanh toán và trạng thái booking dựa theo loại phòng quản lý
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function totalBookingByManagerRevenue($date_start, $date_end, $view)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('rooms.is_manager as manager'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' and bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as revenue'),

                DB::raw('sum(case when bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as total_revenue'),
                DB::raw($selectRawView)
            )
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('createdAt,rooms.is_manager'))
            ->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertTotalBookingManager = $this->convertTotalBookingManager($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data' => $convertTotalBookingManager,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại phòng quản lý
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertTotalBookingManager($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'manager_txt'   => Room::ROOM_MANAGER[$value->manager],
                'manager'       => $value->manager,
                'revenue'       => $value->revenue,
                'total_revenue' => $value->total_revenue,
            ];
        }
        return $convertBooking;
    }

    /**
     * tính tổng tiền của booking theo trạng thái thanh toán và trạng thái booking dựa theo kiểu phòng
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function totalBookingByRoomType($date_start, $date_end, $view)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('rooms.room_type as room_type'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' and bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as revenue'),

                DB::raw('sum(case when bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as total_revenue'),
                DB::raw($selectRawView)
            )
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('createdAt,rooms.room_type'))
            ->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertTotalBookingSource = $this->convertTotalBookingSource($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data' => $convertTotalBookingSource,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại phòng (thống kê doanh thu)
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertTotalBookingSource($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'room_type_txt' => Room::ROOM_TYPE[$value->room_type],
                'room_type'     => $value->room_type,
                'revenue'       => $value->revenue,
                'total_revenue' => $value->total_revenue,
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking theo trạng thái thanh toán và trạng thái booking dựa theo kiểu phòng
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function countBookingByRoomType($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('rooms.room_type as room_type'),
                DB::raw('count(bookings.id) as total_booking'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->whereRaw('bookings.room_id = rooms.id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
            
        if($status != null ) {
            $bookings->where('bookings.status',$status);
        }
        
        $bookings = $bookings->groupBy(DB::raw('createdAt,rooms.room_type'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingSource = $this->convertCountBookingSource($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data' => $convertCountBookingSource,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại phòng (đếm số lượng booking)
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountBookingSource($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value->createdAt === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'room_type_txt' => Room::ROOM_TYPE[$value->room_type],
                'room_type'     => $value->room_type,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }
        return $convertBooking;
    }
}