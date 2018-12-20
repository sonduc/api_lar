<?php

namespace App\Repositories\Referrals;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', // người invite
        'refer_id', // người được invite
        'status'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    const AWARDED      = 1;
    const PENDING      = 0;

    const STATUS = [
        self::AWARDED   => 'Đã trao thưởng',
        self::PENDING   => 'Chưa trao thưởng',
    ];
}
