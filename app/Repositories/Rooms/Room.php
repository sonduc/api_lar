<?php

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    // Quản lý loại phòng
    // Phòng tự quản lý
    const MANAGER_ACTIVE   = 1;
    const MANAGER_DEACTIVE = 0;

    const ROOM_MANAGER = [
        self::MANAGER_ACTIVE   => 'Tự quản lý',
        self::MANAGER_DEACTIVE => 'Không quản lý',
    ];

    // Giờ Checkin , checkout mặc định
    const CHECKIN  = "14:00";
    const CHECKOUT = "12:00";

    // Định nghĩa phòng theo thời gian
    const TYPE_HOUR = 1; // Theo giờ
    const TYPE_DAY  = 2; // Theo ngày
    const TYPE_ALL  = 3; // Theo ngày - giờ

    // Định nghĩa trạng thái phòng
    const NOT_APPROVED   = 0;
    const AVAILABLE      = 1;
    const UNAVAILABLE    = 2;
    const CLEANED        = 3;
    const SETUP_SERVICES = 4;

    // Kiểu phòng
    const PRIVATE_HOUSE = 1;
    const APARTMENT     = 2;
    const VILLA         = 3;
    const PRIVATE_ROOM  = 4;
    const HOTEL         = 5;

    const ROOM_TYPE = [
        self::PRIVATE_HOUSE => 'Nhà riêng',
        self::APARTMENT     => 'Căn hộ/ Chung cư',
        self::VILLA         => 'Biệt thự',
        self::PRIVATE_ROOM  => 'Phòng riêng',
        self::HOTEL         => 'Khách sạn',
    ];

    const ROOM_STATUS = [
        self::AVAILABLE      => 'Đang hoạt động',
        self::UNAVAILABLE    => 'Không hoạt động',
        self::NOT_APPROVED   => 'Chưa xác nhận',
        self::CLEANED        => 'Dọn dẹp phòng',
        self::SETUP_SERVICES => 'Thiết lập dịch vụ',
    ];

    const ROOM_RENT_TYPE = [
        self::TYPE_HOUR => 'Theo giờ',
        self::TYPE_DAY  => 'Theo ngày',
        self::TYPE_ALL  => 'Cả ngày và giờ',
    ];

    // AVG Rating
    const DISAPPOINTED = 'Không hài lòng';
    const NOT_GOOD     = 'Không được như mong muốn';
    const NORMAL       = 'Khá ổn';
    const GOOD         = 'Tốt';
    const EXCELLENT    = 'Rất tuyệt vời';
    const NULL_REVIEW  = 'Chưa có đánh giá';
    const FINISHED     = 6;  // Đây là mốc tiêu chí phải hoàn thành chủa chủ host

    const PRICE_RANGE_RECOMMEND = 150000;


    /**
     * setting-room
     */

    protected $table = 'rooms';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'merchant_id',
        'max_guest',
        'max_additional_guest',
        'number_bed',
        'number_room',
        'city_id',
        'district_id',
        'room_type',
        'checkin',
        'checkout',
        'price_day',
        'price_hour',
        'price_charge_guest',
        'cleaning_fee',
        'standard_point',
        'is_manager',
        'hot',
        'new',
        'latest_deal',
        'rent_type',
        'rules',
        'longitude',
        'latitude',
        'status',
        'sale_id',
        'price_after_hour',
        'avg_cleanliness',
        'avg_quality',
        'avg_service',
        'avg_valuable',
        'avg_avg_rating',
        'total_review',
        'total_recommend',
        'airbnb_calendar',
        'settings',
        'percent',
        'comission'

    ];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $date = ['deleted_at'];
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Transformer alias for eager loading
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return array
     */
    public function transformerAlias()
    {
        return [
            'details' => 'roomTrans',
        ];
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomTrans()
    {
        $locale = getLocale();
        return $this->hasMany(RoomTranslate::class)->where('room_translates.lang', $locale);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'merchant_id');
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comforts()
    {
        return $this->belongsToMany(\App\Repositories\Comforts\Comfort::class, 'room_comforts', 'room_id', 'comfort_id');
    }

    public function places()
    {
        return $this->belongsToMany(\App\Repositories\Places\Place::class, 'room_places', 'room_id', 'place_id');
    }

    public function prices()
    {
        return $this->hasMany(RoomOptionalPrice::class, 'room_id');
    }

    public function blocks()
    {
        return $this->hasMany(RoomTimeBlock::class, 'room_id');
    }

    public function media()
    {
        return $this->hasMany(RoomMedia::class, 'room_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Repositories\Cities\City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(\App\Repositories\Districts\District::class, 'district_id');
    }

    public function bookings()
    {
        return $this->hasMany(\App\Repositories\Bookings\Booking::class, 'room_id');
    }

    /**
     * Relation ship room_reviews
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function reviews()
    {
        return $this->hasMany(RoomReview::class, 'room_id');
    }

    public function transactions()
    {
        return $this->hasMany(\App\Repositories\Transactions\Transaction::class, 'room_id');
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomTransAdmin()
    {
        return $this->hasMany(RoomTranslate::class);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomTransMerchant()
    {
        return $this->hasMany(RoomTranslate::class);
    }
}
