<?php

namespace App\Repositories\TransactionTypes;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionType extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    const TRANSACTION_BOOKING       = 1;
    const TRANSACTION_PENALTY       = 2;
    const TRANSACTION_SURCHARGE     = 3;
    const TRANSACTION_DISCOUNT      = 4;
    const TRANSACTION_PAYOUT        = 5;
    const TRANSACTION_BONUS         = 6;
    const TRANSACTION_BOOK_AIRBNB   = 7;
    const TRANSACTION_BOOK_BOOKING  = 8;
    const TRANSACTION_BOOK_AGODA    = 9;
    const TRANSACTION_RECEIPT       = 10;

    const TYPE = [
        self::TRANSACTION_BOOKING       => 'Đặt phòng Westay',
        self::TRANSACTION_PENALTY       => 'Phạt host',
        self::TRANSACTION_SURCHARGE     => 'Phụ thu',
        self::TRANSACTION_DISCOUNT      => 'Giảm giá',
        self::TRANSACTION_PAYOUT        => 'Phiếu chi',
        self::TRANSACTION_BONUS         => 'Thưởng host',
        self::TRANSACTION_BOOK_AIRBNB   => 'Đặt phòng từ Airbnb',
        self::TRANSACTION_BOOK_BOOKING  => 'Đặt phòng từ Booking',
        self::TRANSACTION_BOOK_AGODA    => 'Đặt phòng từ Agoda',
        self::TRANSACTION_RECEIPT       => 'Phiếu thu'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
