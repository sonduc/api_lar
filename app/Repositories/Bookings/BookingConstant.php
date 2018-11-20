<?php

namespace App\Repositories\Bookings;

final class BookingConstant
{
    const PREFIX     = 'HM';
    const TIME_BLOCK = 4;
    const TIME_DAY   = 24;

    // Khoảng thời gian trống giữa các lần book
    const MINUTE_BETWEEN_BOOK = 60;
    // Trạng thái booking:
    const ACTIVE   = 1;
    const INACTIVE = 0;

    const STATUS = [
        self::ACTIVE   => 'Đang hoạt động',
        self::INACTIVE => 'Đã khóa',
    ];

    // Trạng thái email nhắc nhở
    const EMAIL_SENT    = 1;
    const EMAIL_PENDING = 0;

    const EMAIL_REMINDER = [
        self::EMAIL_SENT    => 'Đã gửi email nhắc nhở',
        self::EMAIL_PENDING => 'Chưa gửi email nhắc nhở',
    ];

    // Định nghĩa trạng thái đặt phòng
    const BOOKING_NEW      = 1; // Đơn mới
    const BOOKING_CONFIRM  = 2; // Đã xác nhận
    const BOOKING_USING    = 3;
    const BOOKING_COMPLETE = 4;
    const BOOKING_CANCEL   = 5;

    const BOOKING_STATUS = [
        self::BOOKING_NEW      => 'Đơn mới',
        self::BOOKING_CONFIRM  => 'Đã xác nhận',
        self::BOOKING_USING    => 'Đang sử dụng',
        self::BOOKING_COMPLETE => 'Đã hoàn thành',
        self::BOOKING_CANCEL   => 'Đã hủy',
    ];

    // Định nghĩa đặt phòng theo
    const BOOKING_TYPE_HOUR = 1; // Theo giờ
    const BOOKING_TYPE_DAY  = 2; // Theo ngày

    const BOOKING_TYPE = [
        self::BOOKING_TYPE_DAY  => 'Theo ngày',
        self::BOOKING_TYPE_HOUR => 'Theo giờ',
    ];

    // Định nghĩa kiểu đặt phòng online hay offline
    const OFFLINE = 1;
    const ONLINE  = 2;

    const TYPE = [
        self::OFFLINE => 'Đặt booking offline',
        self::ONLINE  => 'Đặt booking online',
    ];

    // Định nghĩa phương thức thanh toán
    const COD    = 1; // Tiền mặt
    const CARD   = 2; // Chuyển khoản
    const BAOKIM = 3; // Bảo kim
    const ATM    = 4; // Thanh toán Internet Banking
    const VISA   = 5; // Thanh toán qua thẻ visa hoặc master-card

    const PAYMENT_METHOD = [
        self::COD    => 'Tiền mặt',
        self::CARD   => 'Chuyển khoản',
        self::BAOKIM => 'Bảo kim',
        self::ATM    => 'Internet Banking',
        self::VISA   => 'Thẻ Visa/MasterCard',
    ];

    // Trạng thái thanh toán
    const FAIL           = 1; // Chưa thanh toán
    const DEBT           = 2; // Khách còn nợ
    const PAID           = 3; // Đã thanh toán
    const PENDING        = 0;
    const PAYMENT_STATUS = [
        self::FAIL    => 'Thất bại',
        self::DEBT    => 'Khách còn nợ',
        self::PAID    => 'Đã thanh toán',
        self::PENDING => 'Chờ thanh toán',
    ];

    const UNCONFIRMED = 0; // Chưa được xác nhận thanh toán
    const CONFIRM     = 1; // Xác nhận thanh toán

    // Kiểu thanh toán
    const PAY_IN  = 1; // Thu tiền
    const PAY_OUT = 2; // Xuất tiền

    const PAYMENT_HISTORY_TYPE = [
        self::PAY_IN  => 'Thu tiền',
        self::PAY_OUT => 'Xuất tiền',
    ];

    // Nguồn đặt booking
    const FANPAGE = 1;
    const HOTLINE = 2;
    const CHATBOT = 3;
    const WEBSITE = 4;
    const AIRBNB  = 5;
    const BOOKING = 6;

    const BOOKING_SOURCE = [
        self::FANPAGE => 'Trang fanpage',
        self::HOTLINE => 'Tổng đài',
        self::CHATBOT => 'Qua Sale Team',
        self::WEBSITE => 'Qua Website',
        self::AIRBNB  => 'Qua AirBnb',
        self::BOOKING => 'Qua Booking.com',
    ];

    // Khoảng giá
    const PRICE_RANGE = [
        1  => 'Dưới 200k',
        2  => 'Từ 200k - 500k',
        3  => 'Từ 500k - 750k',
        4  => 'Từ 750k - 1000k',
        5  => 'Từ 1000k - 1300k',
        6  => 'Từ 1300k - 1600k',
        7  => 'Từ 1600k - 2000k',
        8  => 'Từ 2000k - 2500k',
        9  => 'Từ 2500k - 3000k',
        10 => 'Từ 3000k - 3500k',
        11 => 'Từ 3500k - 4000k',
        12 => 'Từ 4000k - 4500k',
        13 => 'Từ 4500k - 5000k',
        14 => 'Trên 5000k',
    ];

    const PRICE_RANGE_LIST = [
        1  => 200,
        2  => 500,
        3  => 750,
        4  => 1000,
        5  => 1300,
        6  => 1600,
        7  => 2000,
        8  => 2500,
        9  => 3000,
        10 => 3500,
        11 => 4000,
        12 => 4500,
        13 => 5000,
    ];

    // Trạng thái payment history
    const UNPAID      = 0;
    const PARTLY_PAID = 1;
    const FULLY_PAID  = 2;

    const PAYMENT_HISTORY_STATUS = [
        self::UNPAID      => 'Chưa thanh toán',
        self::PARTLY_PAID => 'Thanh toán một phần',
        self::FULLY_PAID  => 'Thanh toán hoàn tất',
    ];
}