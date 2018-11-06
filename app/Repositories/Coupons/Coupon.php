<?php

namespace App\Repositories\Coupons;

use App\Repositories\Promotions\Promotion;
use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code','discount','max_discount','usable','used','status','settings','promotion_id'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * ralation ship với promotions
     * @author sonduc <ndson1998@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Promotions()
    {
        return $this->belongsTo(Promotion::class,'promotion_id', 'id');
    }


    public function room_setting()
    {
        
    }
}
