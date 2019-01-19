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

        $data_booking_by_status = $this->booking->countBookingByStatus($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);

        $series_arr = [];
        $some_date  = [];
        foreach ($data_booking_by_status as $key_list_city => $value_list_city) {
            // dd($value_list_city);
            $date_arr[] = $value_list_city['createdAt'];
            $some_date['_' . $value_list_city['createdAt']]  = 0;

            $series_arr['data'][0]['data'][] = (int) $value_list_city['success'];
            $series_arr['data'][0]['name']      = "Thành công";
            $series_arr['data'][1]['data'][]  = (int) $value_list_city['cancel'];
            $series_arr['data'][1]['name']      = "Hủy";
        }

        return [$date_arr, $series_arr];
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

        $data_booking_by_city = $this->booking->countBookingByCity($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
        $series_arr = [];
        $some_date  = [];
        foreach ($data_booking_by_city as $key_list_city => $value_list_city) {
            $date_arr[] = key($value_list_city);
            $some_date['_' . key($value_list_city)]  = 0;

            foreach ($result_city as $key_city => $value_city) {
                $series_arr[$key_city]['name'] = $value_city->name;

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
        $dataInput          = $this->checkInputDataBookingStatistical($data);
        $result_district    = $this->booking->getDistrictHasBooking();

        $data_booking_by_district = $this->booking->countBookingByDistrict($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);

        $data_booking_by_district = $this->booking->countBookingByDistrict($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
        $series_arr = [];
        $some_date  = [];
        $district_arr   = [];
        // dd($list_district);
        // dd($data_booking_by_district);
        foreach ($data_booking_by_district as $key_list_district => $value_list_district) {
            $date_arr[] = key($value_list_district);
            $some_date['_' . key($value_list_district)]  = 0;

            foreach ($result_district as $key_district => $value_district) {
                $series_arr[$key_district]['name'] = $value_district->name;
                // dd($series_arr);
                foreach ($value_list_district as $data_value) {
                    foreach ($data_value as $k => $v) {
                        if ($value_district->id == $v['district_id']) {
                            $series_arr[$key_district]['data']['_' . $v['createdAt']]    = $v['total_booking'];
                            $series_arr[$key_district]['success']['_' . $v['createdAt']] = (int)$v['success'];
                            $series_arr[$key_district]['cancel']['_' . $v['createdAt']]  = (int)$v['cancel'];
                        }
                    }
                }
            }
        }

        foreach ($series_arr as $key => $serie) {
            $serie_data                  = !empty($serie['data']) ? $serie['data'] : [];
            $success                     = !empty($serie['success']) ? $serie['success'] : [];
            $cancel                      = !empty($serie['cancel']) ? $serie['cancel'] : [];
            $results_serie_district          = array_values(array_merge($some_date, $serie_data));
            $series_arr[$key]['data']    = $results_serie_district;
            $series_arr[$key]['success'] = array_values(array_merge($some_date, $success));
            $series_arr[$key]['cancel']  = array_values(array_merge($some_date, $cancel));
        }
        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo loại booking
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByTypeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_type = $this->booking->countBookingByType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
        // dd($data_booking_by_type);
        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];

        foreach ($data_booking_by_type as $key_list_type => $value_list_type) {
            // dd($value_list_type);
            $date_arr[] = key($value_list_type);
            $some_date['_' . key($value_list_type)]  = 0;
            // dd($value_list_type);
            foreach ($value_list_type as $data_value) {
                // dd($data_value);
                foreach ($data_value as $k => $v) {
                    // dd($v);
                    // if ($value_district->id == $v['district_id']) {
                    $series_arr[$k]['name']      = $v['type_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking'];
                    $series_arr[$k]['success'][] = (int)$v['success'];
                    $series_arr[$k]['cancel'][]  = (int)$v['cancel'];

                    // }
                }
            }
        }

        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê doanh thu của booking theo trạng thái checkout
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_revenue = $this->booking->totalBookingByRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
        $date_arr = [];
        $series_arr = [];
        
        foreach ($data_booking_revenue as $key_list_revenue => $value_list_revenue) {
            $date_arr[] = key($value_list_revenue);

            foreach ($value_list_revenue as $value_revenue) {
                $series_arr_total['name'] = 'Tiền về';
                $series_arr_total['data'][]    = (int)$value_revenue['total_revenue'];
            }

            foreach ($value_list_revenue as $v) {
                $series_arr_revenue['name'] = 'Doanh thu';
                $series_arr_revenue['data'][]    = (int)$value_revenue['revenue'];
            }
        }
        
        return [$date_arr, [$series_arr_total, $series_arr_revenue]];
    }

    /**
     * Thống kê doanh thu của booking theo loại phòng quản lý
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByManagerRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_manager_revenue = $this->booking->totalBookingByManagerRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];
        // dd($data_booking_by_manager_revenue);
        foreach ($data_booking_by_manager_revenue as $key_list_type => $value_list_type) {
            // dd($value_list_type);
            $date_arr[] = key($value_list_type);
            foreach ($value_list_type as $data_value) {
                // dd($data_value);
                if (count($data_value) == 2) {
                    foreach ($data_value as $k => $v) {
                        // dd($v);
                        // if ($value_district->id == $v['district_id']) {
                        $series_arr[$k]['name']      = $v['manager_txt'];
                        $series_arr[$k]['data'][]    = (int)$v['revenue'];
                        $series_arr[$k]['total_revenue'][] = (int)$v['total_revenue'];

                        // }
                    }
                } elseif (count($data_value) == 1) {
                    // dd($data_value);
                    if ($data_value[0]['manager'] == 1) {
                        $type_missing = 0;
                        $type_missing_txt = "Tự quản lý";
                    } else {
                        $type_missing = 1;
                        $type_missing_txt = "Không quản lý";
                    }
                    $missing = [
                        "manager_txt" => $type_missing_txt,
                        "manager" => $type_missing,
                        "revenue" => 0,
                        "total_revenue" => 0
                    ];
                    array_push($data_value, $missing);
                    foreach ($data_value as $k => $v) {
                        // var_dump($k);
                        $series_arr[$k]['name'] = $v['manager_txt'];
                        $series_arr[$k]['data'][] = (int)$v['revenue'];
                        $series_arr[$k]['total_revenue'][] = (int)$v['total_revenue'];
                    }
                }
            }
        }
        // dd($series_arr);

        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRoomTypeRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_room_type_revenue = $this->booking->totalBookingByRoomTypeRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);

        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];
        foreach ($data_booking_by_room_type_revenue as $key_list_type => $value_list_type) {
            $date_arr[] = key($value_list_type);
            foreach ($value_list_type as $data_value) {
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['room_type_txt'];
                    $series_arr[$k]['data'][]    = (int)$v['revenue'];
                    $series_arr[$k]['total_revenue'][] = (int)$v['total_revenue'];
                }
            }
        }

        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByRoomTypeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_room_type = $this->booking->countBookingByRoomType($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);

        foreach ($data_booking_by_room_type as $key_list_type => $value_list_type) {
            // dd($value_list_type);
            $date_arr[] = key($value_list_type);
            $some_date['_' . key($value_list_type)]  = 0;
            // dd($value_list_type);
            foreach ($value_list_type as $data_value) {
                // dd($data_value);
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['room_type_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking'];
                    $series_arr[$k]['success'][] = (int)$v['success'];
                    $series_arr[$k]['cancel'][]  = (int)$v['cancel'];
                }
            }
        }

        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo giới tính
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingBySexStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_gender = $this->booking->countBookingBySex($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];

        foreach ($data_booking_by_gender as $key_list_gender => $value_list_gender) {
            $date_arr[] = key($value_list_gender);
            $some_date['_' . key($value_list_gender)]  = 0;
            foreach ($value_list_gender as $data_value) {
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['sex_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking'];
                    $series_arr[$k]['success'][] = (int)$v['success'];
                    $series_arr[$k]['cancel'][]  = (int)$v['cancel'];
                }
            }
        }

        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo khoảng giá
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByPriceRangeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_price_range =  $this->booking->countBookingByPriceRange($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);

        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];

        foreach ($data_booking_by_price_range as $key_list_price_range => $value_list_price_range) {
            $date_arr[] = key($value_list_price_range);
            $some_date['_' . key($value_list_price_range)]  = 0;
            foreach ($value_list_price_range as $data_value) {
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['price_range_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking'];
                    $series_arr[$k]['success'][] = (int)$v['success'];
                    $series_arr[$k]['cancel'][]  = (int)$v['cancel'];
                }
            }
        }
        
        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo khoảng tuổi
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByAgeRangeStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_age_range = $this->booking->countBookingByAgeRange($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
        
        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];

        foreach ($data_booking_by_age_range as $key_list_age_range => $value_list_age_range) {
            $date_arr[] = key($value_list_age_range);
            $some_date['_' . key($value_list_age_range)]  = 0;
            foreach ($value_list_age_range as $data_value) {
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['price_range_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking'];
                    $series_arr[$k]['success'][] = (int)$v['success'];
                    $series_arr[$k]['cancel'][]  = (int)$v['cancel'];
                }
            }
        }
        
        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê trạng thái của booking theo nguồn đặt phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingBySourceStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_source = $this->booking->countBookingBySource($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
                
        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];

        foreach ($data_booking_by_source as $key_list_source => $value_list_source) {
            $date_arr[] = key($value_list_source);
            $some_date['_' . key($value_list_source)]  = 0;
            foreach ($value_list_source as $data_value) {
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['source_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking'];
                    $series_arr[$k]['success'][] = (int)$v['success'];
                    $series_arr[$k]['cancel'][]  = (int)$v['cancel'];
                }
            }
        }
        
        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByTypeRevenueStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_type_revenue = $this->booking->totalBookingByTypeRevenue($dataInput['date_start'], $dataInput['date_end'], $dataInput['view']);
                
        $date_arr   = [];
        $series_arr = [];

        foreach ($data_booking_by_type_revenue as $key_list_revenue => $value_list_revenue) {
            $date_arr[] = key($value_list_revenue);

            foreach ($value_list_revenue as $value_revenue) {
                // dd($value_revenue);
                if (count($value_revenue) == 2) {
                    foreach ($value_revenue as $k => $v) {
                        // var_dump($k);
                        $series_arr[$k]['name'] = $v['type_txt'];
                        $series_arr[$k]['data'][] = (int)$v['total_revenue'];
                        $series_arr[$k]['revenue'][] = (int)$v['revenue'];
                    }
                } elseif (count($value_revenue) == 1) {
                    if ($value_revenue[0]['type'] == 1) {
                        $type_missing = 2;
                        $type_missing_txt = "Theo ngày";
                    } else {
                        $type_missing = 1;
                        $type_missing_txt = "Theo giờ";
                    }
                    $missing = [
                        "type_txt" => $type_missing_txt,
                        "type" => $type_missing,
                        "revenue" => 0,
                        "total_revenue" => 0
                    ];
                    array_push($value_revenue, $missing);
                    foreach ($value_revenue as $k => $v) {
                        // var_dump($k);
                        $series_arr[$k]['name'] = $v['type_txt'];
                        $series_arr[$k]['data'][] = (int)$v['total_revenue'];
                        $series_arr[$k]['revenue'][] = (int)$v['revenue'];
                    }
                }
            }
        }
        
        return [$date_arr, $series_arr];
    }

    /**
     * Thống kê số lượng booking bị hủy theo các lý do hủy phòng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function bookingByCancelStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        $data_booking_by_cancel_reason = $this->booking->countBookingByCancel($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);

        $date_arr   = [];
        $series_arr = [];
        $some_date  = [];

        foreach ($data_booking_by_cancel_reason as $key_list_type_cancel => $value_list_type_cancel) {
            $date_arr[] = key($value_list_type_cancel);
            $some_date['_' . key($value_list_type_cancel)]  = 0;
            foreach ($value_list_type_cancel as $data_value) {
                foreach ($data_value as $k => $v) {
                    $series_arr[$k]['name']      = $v['booking_cancel_code_txt'];
                    $series_arr[$k]['data'][]    = $v['total_booking_cancel'];
                }
            }
        }
        
        return [$date_arr, $series_arr];
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
        // dd($dataInput);
        $today     = Carbon::now()->startOfDay()->toDateString();
        $yesterday = Carbon::now()->subweek()->startOfDay()->toDateString();
        $dataInput['date_start'] = isset($dataInput['date_start']) ? $dataInput['date_start'] : $yesterday ;
        $dataInput['date_end'] = isset($dataInput['date_end']) ? $dataInput['date_end'] : $today;
        return $this->room->countRoomByTopBooking($dataInput['lang'], $dataInput['take'], $dataInput['sort'], $dataInput['date_start'], $dataInput['date_end']);
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
    /**
     * Thống kê số lượng phòng theo tỉnh thành
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function roomByCityStatistical($data)
    {
        $dataInput = $this->checkInputDataBookingStatistical($data);

        return $this->room->countRoomByCity($dataInput['date_start'], $dataInput['date_end'], $dataInput['view'], $dataInput['status']);
    }
}
