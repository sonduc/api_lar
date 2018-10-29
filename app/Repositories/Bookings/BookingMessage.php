<?php

namespace App\Repositories\Bookings;


final class BookingMessage
{
    public const ERR_TIME_BETWEEN_BOOK      = 'error/booking.TIME_BETWEEN_BOOK';
    public const ERR_BOOKING_HOUR_INVALID   = 'error/booking.BOOKING_HOUR_INVALID';
    public const ERR_SHORTER_THAN_TIMEBLOCK = 'error/booking.SHORTER_THAN_TIMEBLOCK';
    public const ERR_BOOKING_INVALID_DAY    = 'error/booking.BOOKING_INVALID_DAY';
    public const ERR_SCHEDULE_BLOCK         = 'error/booking.SCHEDULE_BLOCK';
    public const ERR_BOOKING_CANCEL_ALREADY = 'error/booking.BOOKING_CANCEL_ALREADY';
    // Validate Message
    public const ERR_BOOKING_TYPE_INVALID = 'error/validation.booking.BOOKING_TYPE_INVALID';
}