<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Rooms\Room;
use App\User;
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

    public function checkBooking($id)
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

    public function getBookingByCustomerId($id, $params, $size)
    {
        $this->useScope($params);
        return $this->model
            ->where('bookings.customer_id', $id)
            ->paginate($size);
    }

    /**
     * Lấy tất cả các booking theo id chủ host có phân trang
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $size
     *
     * @return mixed
     */

    public function getBookingByMerchantId($id, $params, $size)
    {
        $this->useScope($params);
        return $this->model
            ->where('bookings.merchant_id', $id)
            ->orderBy('bookings.id', 'DESC')
            ->paginate($size);
    }

    /**
     * Lấy tất cả các booking theo ID của bookign
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function getBookingById($id)
    {
        return parent::getById($id);
    }

    /**
     * Cập nhật trạng thái hoàn thành Review cho booking này
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $data
     */
    public function updateHostReview($id, $data)
    {
        $data['host_reviews'] = BookingConstant::COMPLETE;
        parent::update($id, $data->toArray());
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
        $tomorrow           = $dateNow->addDays(2);
        $tomorrow_timestamp = $tomorrow->timestamp;

        $data               = $this->model
            ->where('checkin', '>=', $dateNow_timestamp)
            ->where('checkout', '>=', $dateNow_timestamp)
            ->where('checkin', '<', $tomorrow_timestamp)
            ->whereIn('status', [
                BookingConstant::BOOKING_CONFIRM,
                BookingConstant::BOOKING_USING,
            ])
            ->get();
        return $data;
    }

    /**
     * Lấy tất cả các bản ghi qua ngày checkout trong khoảng 36h
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getAllBookingCheckoutOneDay()
    {
        $dateNow             = Carbon::now();
        $dateNow_timestamp   = Carbon::now()->timestamp;
        $yesterday_timestamp = $dateNow->subHours(36)->timestamp;
        $data                = $this->model
            ->where('checkout', '<', $dateNow_timestamp)
            ->where('checkout', '>', $yesterday_timestamp)
            ->where('status', BookingConstant::BOOKING_COMPLETE)
            ->get();
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
     * Xử lí dữ liệu ngày, tháng, năm để thống kê booking theo trường create_at
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
     * Xử lí dữ liệu ngày, tháng, năm để thống kê booking theo trường checkout
     * @param  [type] $view [description]
     * @return [type]       [description]
     */
    public function switchViewBookingCheckout($view)
    {
        switch ($view) {
            case 'day':
                $selectRawView = "cast(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d') AS DATE) AS checkout_day";
                break;
            case 'month':
                $selectRawView = "DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'),INTERVAL (1 - DAYOFMONTH(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'))) DAY),'%m-%Y') AS checkout_day";
                break;
            case 'year':
                $selectRawView = "DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'),INTERVAL (1 - DAYOFMONTH(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'))) DAY),'%Y') AS checkout_day";
                break;
            default:
                $selectRawView = "CONCAT(CAST(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'),INTERVAL (1 - DAYOFWEEK(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'))) DAY) AS DATE),' - ',CAST(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'),INTERVAL (7 - DAYOFWEEK(DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d'))) DAY) AS DATE)) AS checkout_day";
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
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )

            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        if ($status != null) {
            $booking->where('bookings.status', $status);
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
                DB::raw('cities. NAME as name_city, cities.id as city_id'),
                DB::raw('count(cities.name) as total_booking'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
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
        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }
        $bookings = $bookings->groupBy(DB::raw('createdAt,name_city'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }
        // dd($bookings);

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCityBooking = $this->convertCityBooking($bookings, $val);

            $convertDataBooking[] = [
                $val      => $convertCityBooking,
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
            // dd($value);
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
                'createdAt'     => $value->createdAt,
                'city_id'       => $value->city_id
            ];
        }
        return $convertBooking;
    }

    /**
     * lấy tất cả thành phố có booking trong khoảng thời gian
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */

    public function getCityHasBooking()
    {
        return $this->model
                            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                            ->join('cities', 'rooms.city_id', '=', 'cities.id')
                            ->select('cities.id', 'cities.name')->distinct()->get();
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
                DB::raw('districts. NAME as name_district, districts.id as district_id'),
                DB::raw('count(bookings.id) as total_booking'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
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

        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings           = $bookings->groupBy(DB::raw('createdAt,name_district'))->get();
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertDistrictBooking = $this->convertDistrictBooking($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertDistrictBooking,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy tất cả Quận huyện có booking trong khoảng thời gian
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */

    public function getDistrictHasBooking()
    {
        return $this->model
                            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                            ->join('districts', 'rooms.district_id', '=', 'districts.id')
                            ->select('districts.id', 'districts.name')->distinct()->get();
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
                'createdAt'     => $value->createdAt,
                'district_id'   => $value->district_id
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
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);

        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings = $bookings->groupBy(DB::raw('createdAt,type'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        // dd($bookings);
        foreach ($date_unique as $k => $val) {
            $convertBookingType = $this->convertBookingType($bookings, $val);

            $convertDataBooking[] = [
                $val      => $convertBookingType,
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
        // dd($dataBooking);
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
     * thống kê doanh thu
     */
    public function statisticalRevenue($date_start, $date_end, $view, $query = [])
    {
        $selectRawView = $this->switchViewBookingCheckout($view);
        if (isset($query['generalSelect'])) {
            $bookings = $this->model->select(
                DB::raw($query['generalSelect']),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' and bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as revenue'),
                DB::raw($selectRawView)
            );
        } else {
            $bookings = $this->model
                ->select(
                    DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' and bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as revenue'),
                    DB::raw($selectRawView)
                );
        }
        if (isset($query['revenueGroupBy']) && $query['revenueGroupBy'] != '' && $query['revenueGroupBy'] != null) {
            $revenueGroupBy = $query['revenueGroupBy'];
        } else {
            $revenueGroupBy = 'checkout_day';
        }

        if (isset($query['generalJoin']) && $query['generalJoin'] != null) {
            foreach ($query['generalJoin'] as $key => $value) {
                $bookings = $bookings->join($key, $value['relasionship_table_id'], $value['condition'], $value['table_id']);
            }
        }
        $bookings = $bookings
            ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d') >= '" . $date_start . "'")
            ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(bookings.checkout), '%Y-%m-%d') <= '" . $date_end . "'");

        if (isset($query['generalWhereRaw']) && $query['generalWhereRaw'] != '' && $query['generalWhereRaw'] != null) {
            $bookings = $bookings->whereRaw($query['generalWhereRaw']);
        }
        $bookings = $bookings
            ->groupBy(DB::raw($revenueGroupBy))
            ->get();
        return $bookings;
    }

    /**
     * thốnh kê tổng tiền về
     */
    public function statisticalTotalRevenue($date_start, $date_end, $view, $query = [])
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);
        if (isset($query['generalSelect']) && $query['generalSelect'] != '' && $query['generalSelect'] != null) {
            $bookings = $this->model->select(
                DB::raw($query['generalSelect']),
                DB::raw('sum(case when bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as total_revenue'),
                DB::raw($selectRawView)
            );
        } else {
            $bookings = $this->model
                ->select(
                    DB::raw('sum(case when bookings.payment_status = ' . BookingConstant::PAID . ' then bookings.total_fee else 0 end) as total_revenue'),
                    DB::raw($selectRawView)
                );
        }

        if (isset($query['totalRevenueGroupBy']) && $query['totalRevenueGroupBy'] != '' && $query['totalRevenueGroupBy'] != null) {
            $totalRevenueGroupBy = $query['totalRevenueGroupBy'];
        } else {
            $totalRevenueGroupBy = 'createdAt';
        }

        if (isset($query['generalJoin']) && $query['generalJoin'] != null) {
            foreach ($query['generalJoin'] as $key => $value) {
                $bookings = $bookings->join($key, $value['relasionship_table_id'], $value['condition'], $value['table_id']);
            }
        }
        $bookings = $bookings
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);

        if (isset($query['generalWhereRaw']) && $query['generalWhereRaw'] != '' && $query['generalWhereRaw'] != null) {
            $bookings = $bookings->whereRaw($query['generalWhereRaw']);
        }
        $bookings = $bookings
            ->groupBy(DB::raw($totalRevenueGroupBy))
            ->get();
        return $bookings;
    }

    /**
     * tính tổng tiền của booking theo trạng thái thanh toán và trạng thái booking(checkout)
     * @param  [type] $date_start [description]
     * @param  [type] $date_end   [description]
     * @return [type]             [description]
     */
    public function totalBookingByRevenue($date_start, $date_end, $view)
    {
        $query                   = [];
        $statisticalRevenue      = $this->statisticalRevenue($date_start, $date_end, $view, $query);
        $statisticalTotalRevenue = $this->statisticalTotalRevenue($date_start, $date_end, $view, $query);

        $arrayRevenue = [];
        foreach ($statisticalRevenue as $key => $value) {
            $arrayRevenue[] = [
                'revenue' => $value->revenue,
                'date'    => $value->checkout_day,
            ];
        }

        $arrayTotalRevenue = [];
        foreach ($statisticalTotalRevenue as $key => $value) {
            $arrayTotalRevenue[] = [
                'total_revenue' => $value->total_revenue,
                'date'          => $value->createdAt,
            ];
        }
        $bookings = [];
        foreach ($arrayRevenue as $key => $value) {
            if (isset($arrayTotalRevenue[$key]['date']) && $value['date'] === $arrayTotalRevenue[$key]['date']) {
                $bookings[][$value['date']] = [
                    'revenue'       => $value['revenue'],
                    'total_revenue' => $arrayTotalRevenue[$key]['total_revenue'],
                    'createdAt'     => $value['date']
                ];
            }
        }
        if (count($arrayRevenue) > count($arrayTotalRevenue)) {
            $result = array_diff_key($arrayRevenue, $arrayTotalRevenue);
        } else {
            $result = array_diff_key($arrayTotalRevenue, $arrayRevenue);
        }
        foreach (array_values($result) as $key => $value) {
            if (isset($value['revenue'])) {
                $bookings[][$value['date']] = [
                    'revenue'       => $value['revenue'],
                    'total_revenue' => 0,
                    'createdAt'     => $value['date']
                ];
            } else {
                $bookings[][$value['date']] = [
                    'revenue'       => 0,
                    'total_revenue' => $value['total_revenue'],
                    'createdAt'     => $value['date']
                ];
            }
        }

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
        $query['generalSelect'] = 'rooms.is_manager as manager';
        $query['generalJoin']   = [
            'rooms' => [
                'relasionship_table_id' => 'rooms.id',
                'condition'             => '=',
                'table_id'              => 'bookings.room_id',
            ],
        ];
        $query['revenueGroupBy']      = 'checkout_day,rooms.is_manager';
        $query['totalRevenueGroupBy'] = 'createdAt,rooms.is_manager';
        $statisticalRevenue           = $this->statisticalRevenue($date_start, $date_end, $view, $query);
        $statisticalTotalRevenue      = $this->statisticalTotalRevenue($date_start, $date_end, $view, $query);

        $arrayRevenue = [];
        foreach ($statisticalRevenue as $key => $value) {
            $arrayRevenue[] = [
                'manager' => $value->manager,
                'revenue' => $value->revenue,
                'date'    => $value->checkout_day,
            ];
        }
        $arrayTotalRevenue = [];
        foreach ($statisticalTotalRevenue as $key => $value) {
            $arrayTotalRevenue[] = [
                'manager'       => $value->manager,
                'total_revenue' => $value->total_revenue,
                'date'          => $value->createdAt,
            ];
        }
        $bookings = [];
        foreach ($arrayRevenue as $key => $value) {
            if (isset($arrayTotalRevenue[$key]['date']) && $value['date'] === $arrayTotalRevenue[$key]['date']) {
                $bookings[] = [
                    'manager'       => $value['manager'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => $arrayTotalRevenue[$key]['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        if (count($arrayRevenue) > count($arrayTotalRevenue)) {
            $result = array_diff_key($arrayRevenue, $arrayTotalRevenue);
        } else {
            $result = array_diff_key($arrayTotalRevenue, $arrayRevenue);
        }
        foreach (array_values($result) as $key => $value) {
            if (isset($value['revenue'])) {
                $bookings[] = [
                    'manager'       => $value['manager'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => 0,
                    'date'          => $value['date'],
                ];
            } else {
                $bookings[] = [
                    'manager'       => $value['manager'],
                    'revenue'       => 0,
                    'total_revenue' => $value['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value['date']);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertTotalBookingManager = $this->convertTotalBookingManager($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertTotalBookingManager
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại phòng quản lý
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertTotalBookingManager($bookings, $date)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value['date'] === $date) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'manager_txt'   => Room::ROOM_MANAGER[$value['manager']],
                'manager'       => $value['manager'],
                'revenue'       => $value['revenue'],
                'total_revenue' => $value['total_revenue'],
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
    public function totalBookingByRoomTypeRevenue($date_start, $date_end, $view)
    {
        $query['generalSelect'] = 'rooms.room_type as room_type';
        $query['generalJoin']   = [
            'rooms' => [
                'relasionship_table_id' => 'rooms.id',
                'condition'             => '=',
                'table_id'              => 'bookings.room_id',
            ],
        ];
        $query['revenueGroupBy']      = 'checkout_day,rooms.room_type';
        $query['totalRevenueGroupBy'] = 'createdAt,rooms.room_type';
        $statisticalRevenue           = $this->statisticalRevenue($date_start, $date_end, $view, $query);
        $statisticalTotalRevenue      = $this->statisticalTotalRevenue($date_start, $date_end, $view, $query);

        $arrayRevenue = [];
        foreach ($statisticalRevenue as $key => $value) {
            $arrayRevenue[] = [
                'room_type' => $value->room_type,
                'revenue'   => $value->revenue,
                'date'      => $value->checkout_day,
            ];
        }
        $arrayTotalRevenue = [];
        foreach ($statisticalTotalRevenue as $key => $value) {
            $arrayTotalRevenue[] = [
                'room_type'     => $value->room_type,
                'total_revenue' => $value->total_revenue,
                'date'          => $value->createdAt,
            ];
        }
        $bookings = [];
        foreach ($arrayRevenue as $key => $value) {
            if (isset($arrayTotalRevenue[$key]['date']) && $value['date'] === $arrayTotalRevenue[$key]['date']) {
                $bookings[] = [
                    'room_type'     => $value['room_type'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => $arrayTotalRevenue[$key]['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        if (count($arrayRevenue) > count($arrayTotalRevenue)) {
            $result = array_diff_key($arrayRevenue, $arrayTotalRevenue);
        } else {
            $result = array_diff_key($arrayTotalRevenue, $arrayRevenue);
        }
        foreach (array_values($result) as $key => $value) {
            if (isset($value['revenue'])) {
                $bookings[] = [
                    'room_type'     => $value['room_type'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => 0,
                    'date'          => $value['date'],
                ];
            } else {
                $bookings[] = [
                    'room_type'     => $value['room_type'],
                    'revenue'       => 0,
                    'total_revenue' => $value['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value['date']);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertTotalBookingSource = $this->convertTotalBookingSource($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertTotalBookingSource
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại phòng (thống kê doanh thu)
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertTotalBookingSource($bookings, $date)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value['date'] === $date) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'room_type_txt' => Room::ROOM_TYPE[$value['room_type']],
                'room_type'     => $value['room_type'],
                'revenue'       => $value['revenue'],
                'total_revenue' => $value['total_revenue'],
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
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->whereRaw('bookings.room_id = rooms.id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings = $bookings->groupBy(DB::raw('createdAt,rooms.room_type'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingType = $this->convertCountBookingType($bookings, $val);

            $convertDataBooking[] = [
                $val      => $convertCountBookingType,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo loại phòng (đếm số lượng booking)
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountBookingType($bookings, $createdAt)
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
                'createdAt'     => $value->createdAt
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking theo giới tính
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingBySex($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('count(sex) as total_booking,sex'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->where('bookings.sex', '!=', 0)
            ->whereRaw('bookings.sex IS NOT NULL')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings = $bookings->groupBy(DB::raw('createdAt,sex'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingSex = $this->convertCountBookingSex($bookings, $val);

            $convertDataBooking[] = [
                $val      => $convertCountBookingSex,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo giới tính
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountBookingSex($bookings, $createdAt)
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
                'sex_txt'       => User::SEX[$value->sex],
                'sex'           => $value->sex,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking theo khoảng giá
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByPriceRange($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('count(id) as total_booking,price_range'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->whereRaw('bookings.price_range IS NOT NULL')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings = $bookings->groupBy(DB::raw('createdAt,price_range'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingPriceRange = $this->convertCountBookingPriceRange($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertCountBookingPriceRange,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo khoảng giá
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountBookingPriceRange($bookings, $createdAt)
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
                'price_range_txt' => BookingConstant::PRICE_RANGE[$value->price_range],
                'price_range'     => $value->price_range,
                'total_booking'   => $value->total_booking,
                'success'         => $value->success,
                'cancel'          => $value->cancel,
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking theo khoảng tuổi
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByAgeRange($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('count(id) as total_booking,age_range'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->whereRaw('bookings.age_range IS NOT NULL')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings = $bookings->groupBy(DB::raw('createdAt,age_range'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingAgeRange = $this->convertCountBookingAgeRange($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertCountBookingAgeRange,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo khoảng tuổi
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertCountBookingAgeRange($bookings, $createdAt)
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
                'age_range_txt' => User::AGE_RANGE[$value->age_range],
                'age_range'     => $value->age_range,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }
        return $convertBooking;
    }

    /**
     *
     * Lấy ra tất cả người ID người dùng có booking đầu tiên và đã checkout
    */
    public function getUserFirstBooking($list_id, $start_checkout, $end_checkout, $total_fee)
    {
        return $this->model->whereIn('bookings.customer_id', $list_id)
        ->where(
            [
            ['checkout', '<=', $end_checkout],
            ['checkout', '>=', $start_checkout],
            ['status','=', BookingConstant::BOOKING_COMPLETE],
            ['payment_status','=',BookingConstant::PAID],
            ['total_fee', '>=', $total_fee]]
        )->pluck('customer_id');
    }

    /**
     *
     * Lấy ra tất cả người ID chủ nhà có booking đầu tiên và đã checkout

     */
    public function getMerchantFirstBooking($list_id, $start_checkout, $end_checkout, $total_fee)
    {
        return $this->model->whereIn('bookings.merchant_id', $list_id)
        ->where(
            [
            ['checkout', '<=', $end_checkout],
            ['checkout', '>=', $start_checkout],
            ['status','=', BookingConstant::BOOKING_COMPLETE],
            ['payment_status','=',BookingConstant::PAID],
            ['total_fee', '>=', $total_fee]]
        )->pluck('merchant_id');
    }

    /**
     * đếm booking theo nguồn booking
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingBySource($date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('count(id) as total_booking,source'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CONFIRM . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->whereRaw('bookings.source IS NOT NULL')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);


        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }
            
        $bookings = $bookings->groupBy(DB::raw('createdAt,source'))->get();
            
        // dd($bookings);
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingSource = $this->convertCountBookingSource($bookings, $val);

            $convertDataBooking[] = [
                $val      => $convertCountBookingSource,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo nguồn booking
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
                'source_txt'    => BookingConstant::BOOKING_SOURCE[$value->source],
                'source'        => $value->source,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
                'createdAt'     => $value->createdAt
            ];
        }
        return $convertBooking;
    }

    /**
     * Thống kê doanh thu booking theo ngày giờ
     * @author sonduc <ndson1998@gmail.com>
     */
    public function totalBookingByTypeRevenue($date_start, $date_end, $view)
    {
        $query['generalSelect']       = 'type';
        $query['generalWhereRaw']     = '';
        $query['revenueGroupBy']      = 'checkout_day,type';
        $query['totalRevenueGroupBy'] = 'createdAt,type';
        $statisticalRevenue           = $this->statisticalRevenue($date_start, $date_end, $view, $query);
        $statisticalTotalRevenue      = $this->statisticalTotalRevenue($date_start, $date_end, $view, $query);
        $arrayRevenue                 = [];
        foreach ($statisticalRevenue as $key => $value) {
            $arrayRevenue[] = [
                'type'    => $value->type,
                'revenue' => $value->revenue,
                'date'    => $value->checkout_day,
            ];
        }
        $arrayTotalRevenue = [];
        foreach ($statisticalTotalRevenue as $key => $value) {
            $arrayTotalRevenue[] = [
                'type'          => $value->type,
                'total_revenue' => $value->total_revenue,
                'date'          => $value->createdAt,
            ];
        }
        $bookings = [];
        foreach ($arrayRevenue as $key => $value) {
            if (isset($arrayTotalRevenue[$key]['date']) && $value['date'] === $arrayTotalRevenue[$key]['date']) {
                $bookings[] = [
                    'type'          => $value['type'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => $arrayTotalRevenue[$key]['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        if (count($arrayRevenue) > count($arrayTotalRevenue)) {
            $result = array_diff_key($arrayRevenue, $arrayTotalRevenue);
        } else {
            $result = array_diff_key($arrayTotalRevenue, $arrayRevenue);
        }
        // dd($result);
        foreach (array_values($result) as $key => $value) {
            if (isset($value['revenue'])) {
                $bookings[] = [
                    'type'          => $value['type'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => 0,
                    'date'          => $value['date'],
                ];
            } else {
                $bookings[] = [
                    'type'          => $value['type'],
                    'revenue'       => 0,
                    'total_revenue' => $value['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value['date']);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertTotalBookingType = $this->convertTotalBookingType($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertTotalBookingType
            ];
        }
        // dd($convertDataBooking);
        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo ngày giờ
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertTotalBookingType($bookings, $date)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value['date'] === $date) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'type_txt'      => BookingConstant::BOOKING_TYPE[$value['type']],
                'type'          => $value['type'],
                'revenue'       => (int) $value['revenue'],
                'total_revenue' => (int) $value['total_revenue'],
            ];
        }
        return $convertBooking;
    }

    /**
     * đếm booking bị hủy theo các lý do hủy phòng
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingByCancel($date_start, $date_end, $view)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->join('booking_cancel', 'bookings.id', '=', 'booking_cancel.booking_id')
            ->select(
                DB::raw('booking_cancel.code as booking_cancel_code'),
                DB::raw('count(booking_cancel.code) as total'),
                DB::raw($selectRawView)
            )
            ->whereRaw('booking_cancel.code IS NOT NULL')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
                ['bookings.status', '=', BookingConstant::BOOKING_CANCEL],
            ])
            ->groupBy(DB::raw('createdAt,booking_cancel.code'))->get();

        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertBookingCancel = $this->convertBookingCancel($bookings, $val);

            $convertDataBooking[] = [
                $val => $convertBookingCancel
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo các lý do hủy phòng
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertBookingCancel($bookings, $createdAt)
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
                'booking_cancel_code_txt' => BookingCancel::getBookingCancel()[$value->booking_cancel_code],
                'booking_cancel_code'     => $value->booking_cancel_code,
                'total_booking_cancel'    => $value->total,
            ];
        }
        return $convertBooking;
    }

    /**
     * Thống kê doanh thu của 1 khách hàng
     */
    public function totalBookingByOneCustomerRevenue($customer_id, $date_start, $date_end, $view)
    {
        $query['generalSelect']       = 'users.name as user_name,bookings.type as type';
        $query['generalWhereRaw']     = 'users.id = '.$customer_id.'';
        $query['generalJoin']         = [
            'users' => [
                'relasionship_table_id' => 'users.id',
                'condition'             => '=',
                'table_id'              => 'bookings.customer_id',
            ],
        ];
        $query['revenueGroupBy']      = 'checkout_day,bookings.type';
        $query['totalRevenueGroupBy'] = 'createdAt,bookings.type';
        $statisticalRevenue           = $this->statisticalRevenue($date_start, $date_end, $view, $query);
        $statisticalTotalRevenue      = $this->statisticalTotalRevenue($date_start, $date_end, $view, $query);

        $arrayRevenue = [];
        foreach ($statisticalRevenue as $key => $value) {
            $arrayRevenue[] = [
                'type'      => $value->type,
                'user_name' => $value->user_name,
                'revenue'   => $value->revenue,
                'date'      => $value->checkout_day,
            ];
        }
        $arrayTotalRevenue = [];
        foreach ($statisticalTotalRevenue as $key => $value) {
            $arrayTotalRevenue[] = [
                'type'      => $value->type,
                'user_name' => $value->user_name,
                'total_revenue' => $value->total_revenue,
                'date'          => $value->createdAt,
            ];
        }
        $bookings = [];
        foreach ($arrayRevenue as $key => $value) {
            if (isset($arrayTotalRevenue[$key]['date']) && $value['date'] === $arrayTotalRevenue[$key]['date']) {
                $bookings[] = [
                    'type'          => $value['type'],
                    'user_name'     => $value['user_name'],
                    'revenue'       => $value['revenue'],
                    'total_revenue' => $arrayTotalRevenue[$key]['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        if (count($arrayRevenue) > count($arrayTotalRevenue)) {
            $result = array_diff_key($arrayRevenue, $arrayTotalRevenue);
        } else {
            $result = array_diff_key($arrayTotalRevenue, $arrayRevenue);
        }
        foreach (array_values($result) as $key => $value) {
            if (isset($value['revenue'])) {
                $bookings[] = [
                    'type'          => $value['type'],
                    'user_name'     => $value['user_name'],
                    'total_revenue' => 0,
                    'date'          => $value['date'],
                ];
            } else {
                $bookings[] = [
                    'type'          => $value['type'],
                    'user_name'     => $value['user_name'],
                    'revenue'       => 0,
                    'total_revenue' => $value['total_revenue'],
                    'date'          => $value['date'],
                ];
            }
        }
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value['date']);
        }
        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertTotalBookingOneCustomer = $this->convertTotalBookingOneCustomer($bookings, $val);

            $convertDataBooking[] = [
                'date' => $val,
                'data' => $convertTotalBookingOneCustomer,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo doanh thu của 1 khách hàng
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function convertTotalBookingOneCustomer($bookings, $date)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value['date'] === $date) {
                $dataBooking[] = $value;
            }
        }
        foreach ($dataBooking as $key => $value) {
            $convertBooking[] = [
                'type_txt'      => BookingConstant::BOOKING_TYPE[$value['type']],
                'type'          => $value['type'],
                'user_name'     => $value['user_name'],
                'revenue'       => $value['revenue'],
                'total_revenue' => $value['total_revenue'],
            ];
        }
        return $convertBooking;
    }

    /**
     * Thống kê số lượng booking theo ngày, theo giờ của một khách hàng
     */
    public function countBookingByTypeOneCustomer($customer_id, $date_start, $date_end, $view, $status)
    {
        $selectRawView = $this->switchViewBookingCreatedAt($view);

        $bookings = $this->model
            ->select(
                DB::raw('users.name as user_name,bookings.type as type'),
                DB::raw('count(bookings.id) as total_booking'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw($selectRawView)
            )
            ->join('users', 'users.id', '=', 'bookings.customer_id')
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
                ['bookings.customer_id', '=', $customer_id],
            ]);

        if ($status != null) {
            $bookings->where('bookings.status', $status);
        }

        $bookings           = $bookings->groupBy(DB::raw('createdAt,bookings.type'))->get();
        $data_date          = [];
        $convertDataBooking = [];
        foreach ($bookings as $key => $value) {
            array_push($data_date, $value->createdAt);
        }

        $date_unique = array_unique($data_date);
        foreach ($date_unique as $k => $val) {
            $convertCountBookingTypeOneCustomer = $this->convertCountBookingTypeOneCustomer($bookings, $val);

            $convertDataBooking[] = [
                'createdAt' => $val,
                'data'      => $convertCountBookingTypeOneCustomer,
            ];
        }

        return $convertDataBooking;
    }

    /**
     * lấy những booking có cùng ngày theo ngày, theo giờ của một khách hàng
     * @author sonduc <ndson1998@gmail.com>
     */
    public function convertCountBookingTypeOneCustomer($bookings, $createdAt)
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
                'user_name'     => $value->user_name,
                'total_booking' => $value->total_booking,
                'success'       => $value->success,
                'cancel'        => $value->cancel,
            ];
        }

        return $convertBooking;
    }

    public function getAllCustomerBooked($date)
    {
        return $this->model->where('created_at', '<=', $date)->where('status', BookingConstant::BOOKING_COMPLETE)->distinct('phone')->pluck('phone');
    }

    public function countOldCustomer($date_start, $date_end, $view, $status)
    {
        $selectRawView  = $this->switchViewBookingCreatedAt($view);
        $customers      = $this->getAllCustomerBooked($date_start);

        $old_customers  = $this->model
            ->select(
                DB::raw($selectRawView),
                DB::raw('sum(case when bookings.status = ' . BookingConstant::BOOKING_COMPLETE .' then 1 else 0 end) as old_customer')
            )
            ->whereIn('phone', $customers)
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);

        $old_customers   = $old_customers->groupBy(DB::raw('createdAt'))->get()->toArray();

        $total_customers = $this->model
            ->select(
                DB::raw($selectRawView),
                DB::raw('count(distinct phone) as total_customer')
            )
            ->whereNotIn('phone', $customers)
            ->where([
                ['bookings.created_at', '>=', $date_start],
                ['bookings.created_at', '<=', $date_end],
            ]);
        $total_customers    = $total_customers->groupBy(DB::raw('createdAt'))->get()->toArray();
        // dd($total_customers, $old_customers);
        $data_date          = [];
        $convertDataBooking = [];
  
        foreach ($old_customers as $k => $val) {
            $result[] = array_merge($old_customers[$k], $total_customers[$k]);
        }

        foreach ($result as $key => $value) {
            // dd($value);
            array_push($data_date, $value['createdAt']);
        }
        $date_unique = array_unique($data_date);
        // dd($result);
        foreach ($date_unique as $k => $val) {
            $converOldCustomer = $this->convertOldCustomer($result, $val);

            $convertDataBooking[] = [
                $val      => $converOldCustomer,
            ];
        }

        return $convertDataBooking;
    }

    public function convertOldCustomer($bookings, $createdAt)
    {
        $dataBooking    = [];
        $convertBooking = [];
        foreach ($bookings as $key => $value) {
            if ($value['createdAt'] === $createdAt) {
                $dataBooking[] = $value;
            }
        }
        // dd($dataBooking);
        foreach ($dataBooking as $key => $value) {
            // dd($value);
            $convertBooking[] = [
                'createdAt'      => $value['createdAt'],
                'total_customer' => $value['total_customer'],
                'old_customer'   => $value['old_customer']
            ];
        }
        return $convertBooking;
    }

    /**
     * Lấy tất cả các bản ghi booking đã qua ngày checkin
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getAllBookingPast($dateNow_timestamp)
    {
        $data               = $this->model
            ->where('checkin', '<=', $dateNow_timestamp)
            ->whereIn('status', [
                BookingConstant::BOOKING_CONFIRM,
                BookingConstant::BOOKING_USING,
            ])
            ->get();
        // dd(count($data));
        return $data;
    }
}
