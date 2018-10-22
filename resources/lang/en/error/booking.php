<?php

use App\Repositories\Bookings\BookingConstant;

return [
    'TIME_BETWEEN_BOOK'      => 'Thời gian checkin phải nhỏ hơn thời gian checkin của phòng ít nhất '
        . BookingConstant::MINUTE_BETWEEN_BOOK . ' phút',
    'SHORTER_THAN_TIMEBLOCK' => 'Thời gian đặt tối thiểu là ' . BookingConstant::TIME_BLOCK . ' giờ',
    'BOOKING_HOUR_INVALID'   => 'Đặt theo giờ chỉ cho phép trong ngày',
    'BOOKING_INVALID_DAY'    => 'Ngày checkin và ngày checkout không được phép trùng nhau',
    'SCHEDULE_BLOCK'         => 'Khoảng ngày vừa chọn không khả dụng. Vui lòng thử lại ngày khác',
];