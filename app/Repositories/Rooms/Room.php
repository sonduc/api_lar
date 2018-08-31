<?php

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'merchant_id', 'max_guest', 'max_additional_guest', 'number_bed', 'number_room', 'city_id', 'district_id',
        'room_type_id', 'checkin', 'checkout', 'price_day', 'price_hour', 'price_charge_guest', 'cleaning_fee',
        'standard_point', 'is_manager', 'hot', 'new', 'latest_deal', 'rent_type', 'rules', 'longitude', 'latitude', 'status', 'sale_id', 'price_after_hour',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $date = ['deleted_at'];


    // Phòng tự quản lý
    const MANAGER_ACTIVE    = 1;
    const MANAGER_DEACTIVE  = 0;

    // Giờ Checkin , checkout mặc định
    const CHECKIN  = "14:00";
    const CHECKOUT = "12:00";

    // Định nghĩa phòng theo thời gian
    const TYPE_HOUR  = 1; // Theo giờ
    const TYPE_DAY   = 2; // Theo ngày
    const TYPE_ALL   = 3; // Theo ngày - giờ

    // Định nghĩa trạng thái phòng
    const NOT_APPROVED      = 0;
    const AVAILABLE         = 1;
    const UNAVAILABLE       = 2;
    const CLEANED           = 3;
    const SETUP_SERVICES    = 4;

    // Kiểu phòng
    const PRIVATE_HOUSE     = 1;
    const APARTMENT         = 2;
    const VILLA             = 3;
    const PRIVATE_ROOM      = 4;
    const HOTEL             = 5;

    const ROOM_TYPE = [
        self::PRIVATE_HOUSE     => 'Nhà riêng',
        self::APARTMENT         => 'Căn hộ/ Chung cư',
        self::VILLA             => 'Biệt thự',
        self::PRIVATE_ROOM      => 'Phòng riêng',
        self::HOTEL             => 'Khách sạn'
    ];

    const ROOM_STATUS  = [
        self::NOT_APPROVED    => 'Chưa xác nhận',
        self::AVAILABLE       => 'Đang hoạt động',
        self::UNAVAILABLE     => 'Không hoạt động',
        self::CLEANED         => 'Dọn dẹp phòng',
        self::SETUP_SERVICES  => 'Thiết lập dịch vụ'
    ];
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Relationship với RoomTranslate
     * @return Relation
     */
    public function roomTrans()
    {
        return $this->hasMany(\App\Repositories\Rooms\RoomTranslate::class);
    }

    /**
     * Relationship với user
     * @return Relation
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'merchant_id');
    }

    /**
     * Relationship với comfort
     *
     * @return Relation
     */
    public function comforts()
    {
        return $this->belongsToMany(\App\Repositories\Comforts\Comfort::class, 'room_comforts', 'room_id', 'comfort_id');
    }
}
