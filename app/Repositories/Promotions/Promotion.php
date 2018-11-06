<?php

namespace App\Repositories\Promotions;

use App\Repositories\Coupons\Coupon;
use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    // Định nghĩa trạng thái promotion
    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;
    const PROMOTION_STATUS    = [
        self::AVAILABLE      => 'ĐANG CHẠY',
        self::UNAVAILABLE    => 'ĐÃ HẾT HẠN',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description','image','date_start','date_end','status'
    ];

    /**
     * [$casts description]
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * relation ship voi coupon
     * @author sonduc <ndson1998@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Coupon()
    {
        return $this->hasMany(Coupon::class,'promotion_id','id');
    }
}
