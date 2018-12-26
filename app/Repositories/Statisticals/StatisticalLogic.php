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
    public function checkDataInputStatistical($data)
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
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByStatus($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo thành phố
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByCityStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByCity($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo quận huyện
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByDistrictStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByDistrict($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo loại booking
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByTypeStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê doanh thu của booking theo trạng thái checkout
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRevenueStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->totalBookingByRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê doanh thu của booking theo loại phòng quản lý
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByManagerRevenueStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->totalBookingByManagerRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRoomTypeRevenueStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->totalBookingByRoomType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê trạng thái của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRoomTypeStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByRoomType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo giới tính
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingBySexStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingBySex($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo khoảng giá
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByPriceRangeStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByPriceRange($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo khoảng tuổi
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByAgeRangeStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByAgeRange($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê trạng thái của booking theo nguồn đặt phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingBySourceStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingBySource($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByTypeRevenueStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->totalBookingByTypeRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
    }

    /**
     * Thống kê số lượng booking bị hủy theo các lý do hủy phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByCancelStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->booking->countBookingByCancel($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê số lượng phòng theo loại phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByTypeStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->room->countRoomByType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê số lượng phòng theo tỉnh thành
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByDistrictStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->room->countRoomByDistrict($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }

    /**
     * Thống kê Top phòng có booking nhiều nhất
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByTopBookingStatistical($data)
    {
        $dataInput = $this->checkDataInputStatistical($data);

        return $this->room->countRoomByTopBooking($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }
}
