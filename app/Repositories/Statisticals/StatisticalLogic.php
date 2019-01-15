<?php

namespace App\Repositories\Statisticals;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;
use Carbon\Carbon;

class StatisticalLogic extends BaseLogic
{
    protected $model;
    protected $booking;
    protected $room;

    public function __construct(
        StatisticalRepositoryInterface $statistical,
        BookingRepositoryInterface $booking,
        RoomRepositoryInterface $room
    ) {
        $this->model   = $statistical;
        $this->booking = $booking;
        $this->room    = $room;
    }

    /**
     * Xử lí dữ liệu đầu vào để thống kê booking
     * @param  [type] $view [description]
     * @return [type]       [description]
     */
    public function checkInputDataBookingStatistical($data)
    {
        $dataInput['status']     = (isset($data['status']) && $data['status'] != null) ? $data['status'] : null;
        $dataInput['view']       = isset($data['view']) ? $data['view'] : 'week';
        $dataInput['date_start'] = isset($data['date_start']) ? $data['date_start'] : Carbon::now()->startOfMonth()->toDateTimeString();
        $dataInput['date_end']   = isset($data['date_end']) ? $data['date_end'] : Carbon::now()->toDateTimeString();
        return $dataInput;
    }

    /**
     * Thống kê trạng thái của booking
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByStatusStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByStatus($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo thành phố
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByCityStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);
        $result_city = $this->booking->getCityHasBooking();
        // dd($result_city);
        $data_booking_by_city = $this->booking->countBookingByCity($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
        $series_arr = [];
        $some_date  = [];
        $city_arr   = [];
        // dd($list_city);
        // dd($data_booking_by_city);
        foreach ($data_booking_by_city as $key_list_city => $value_list_city) {
            $date_arr[] = key($value_list_city);
            $some_date['_' . key($value_list_city)]  = 0;

            foreach ($result_city as $key_city => $value_city) {
                $series_arr[$key_city]['name'] = $value_city->name;
                // dd($series_arr);
                foreach ($value_list_city as $data_value) {
                    foreach ($data_value as $k => $v) {
                        if ($value_city->id == $v['city_id']) {
                            $series_arr[$key_city]['data']['_' . $v['createdAt']]    = $v['total_booking'];
                            $series_arr[$key_city]['success']['_' . $v['createdAt']] = (int)$v['success'];
                            $series_arr[$key_city]['cancel']['_' . $v['createdAt']]  = (int)$v['cancel'];
                        }
                    }
                }
            }
        }

        foreach ($series_arr as $key => $serie) {
            $serie_data                  = !empty($serie['data']) ? $serie['data'] : [];
            $success                     = !empty($serie['success']) ? $serie['success'] : [];
            $cancel                      = !empty($serie['cancel']) ? $serie['cancel'] : [];
            $results_serie_city          = array_values(array_merge($some_date, $serie_data));
            $series_arr[$key]['data']    = $results_serie_city;
            $series_arr[$key]['success'] = array_values(array_merge($some_date, $success));
            $series_arr[$key]['cancel']  = array_values(array_merge($some_date, $cancel));
        }
        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo quận huyện
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByDistrictStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByDistrict($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo loại booking
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByTypeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê doanh thu của booking theo trạng thái checkout
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->totalBookingByRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê doanh thu của booking theo loại phòng quản lý
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByManagerRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->totalBookingByManagerRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRoomTypeRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->totalBookingByRoomType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê trạng thái của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRoomTypeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByRoomType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo giới tính
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingBySexStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingBySex($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo khoảng giá
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByPriceRangeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByPriceRange($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo khoảng tuổi
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByAgeRangeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByAgeRange($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo nguồn đặt phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingBySourceStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingBySource($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByTypeRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->totalBookingByTypeRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê số lượng booking bị hủy theo các lý do hủy phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByCancelStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByCancel($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê số lượng phòng theo loại phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByTypeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->room->countRoomByType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê số lượng phòng theo tỉnh thành
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByDistrictStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->room->countRoomByDistrict($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Xử lí dữ liệu đầu vào để thống kê room
     * @param  [type] $view [description]
     * @return [type]       [description]
     */
    public function checkInputDataRoomStatistical($data)
    {
        $data['lang']       = (isset($data['lang']) && $data['lang'] != '' && $data['lang'] != null) ? $data['lang'] : 'vi';
        $data['take']       = (isset($data['take']) && $data['take'] != '' && $data['take'] != null) ? $data['take'] : '10';
        $data['sort']       = (isset($data['sort']) && $data['sort'] != '' && $data['sort'] != null) ? $data['sort'] : 'desc';

        return $data;
    }
    /**
     * Thống kê Top phòng có booking nhiều nhất
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByTopBookingStatistical($data)
    {
        $dataInput = $this->checkInputDataRoomStatistical($data);

        return $this->room->countRoomByTopBooking($dataInput['lang'], $dataInput['take'], $dataInput['sort']);
    }

    /**
     * Thống kê Top phòng có booking nhiều nhất theo loại phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByTypeTopBookingStatistical($data)
    {
        $dataInput = $this->checkInputDataRoomStatistical($data);

        return $this->room->countRoomByTypeTopBooking($dataInput['lang'], $dataInput['take'], $dataInput['sort']);
    }

    /**
     * Thống kê doanh thu của 1 khách hàng
     */
    public function bookingByOneCustomerRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->totalBookingByOneCustomerRevenue($data['customer_id'], $dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê số lượng booking theo ngày, theo giờ của một khách hàng
     */
    public function bookingByTypeOneCustomerStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->booking->countBookingByTypeOneCustomer($data['customer_id'], $dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }
}
