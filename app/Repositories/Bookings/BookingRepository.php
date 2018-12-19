<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;
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
     * đếm booking trong khoảng ngày và theo trạng thái
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingDay($date_start, $date_end)
    {
        $booking = $this->model
            ->select(
                DB::raw('count(id) as total_booking'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw('cast(created_at as DATE) as date')
            )
            ->where([
                ['created_at', '>=', $date_start],
                ['created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('cast(created_at as DATE)'))
            ->get();
        return $booking;
    }

    /**
     * đếm booking trong khoảng tuần và theo trạng thái
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingWeek($date_start, $date_end)
    {
        $booking = $this->model
            ->select(
                DB::raw('count(id) as total_booking'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw(
                    '
                    CONCAT(
                        CAST(
                            DATE_ADD(
                                created_at,
                                INTERVAL (1 - DAYOFWEEK(created_at)) DAY
                            ) AS DATE
                        ),
                        " - ",
                        CAST(
                            DATE_ADD(
                                created_at,
                                INTERVAL (7 - DAYOFWEEK(created_at)) DAY
                            ) AS DATE
                        )
                    ) AS date'
                )
            )
            ->where([
                ['created_at', '>=', $date_start],
                ['created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('date'))
            ->get();
        return $booking;
    }

    /**
     * đếm booking trong khoảng tháng và theo trạng thái
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingMonth($date_start, $date_end)
    {
        $booking = $this->model
            ->select(
                DB::raw('count(id) as total_booking'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw(
                    '
                    DATE_FORMAT(
                        DATE_ADD(
                            created_at,
                            INTERVAL (1 - DAYOFMONTH(created_at)) DAY
                        ),
                        "%m-%Y"
                    ) AS date'
                )
            )
            ->where([
                ['created_at', '>=', $date_start],
                ['created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('date'))
            ->get();
        return $booking;
    }

    /**
     * đếm booking trong khoảng năm và theo trạng thái
     * @author sonduc <ndson1998@gmail.com>
     * @return [type] [description]
     */
    public function countBookingYear($date_start, $date_end)
    {
        $booking = $this->model
            ->select(
                DB::raw('count(id) as total_booking'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_COMPLETE . ' then 1 else 0 end) as success'),
                DB::raw('sum(case when `status` = ' . BookingConstant::BOOKING_CANCEL . ' then 1 else 0 end) as cancel'),
                DB::raw(
                    '
                    DATE_FORMAT(
                        DATE_ADD(
                            created_at,
                            INTERVAL (1 - DAYOFMONTH(created_at)) DAY
                        ),
                        "%Y"
                    ) AS date'
                )
            )
            ->where([
                ['created_at', '>=', $date_start],
                ['created_at', '<=', $date_end],
            ])
            ->groupBy(DB::raw('date'))
            ->get();
        return $booking;
    }

    public function getUserFirstBooking($list_id, $start_checkout, $end_checkout)
    {
        return $this->model->whereIn('bookings.customer_id', $list_id)->where('checkout', '>=', $start_checkout)->where('checkout', '<=', $end_checkout)->get();
    }
}
