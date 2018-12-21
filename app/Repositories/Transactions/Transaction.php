<?php

namespace App\Repositories\Transactions;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'credit',
        'debit',
        'date_create',
        'user_id',
        'booking_id',
        'room_id',
        'bonus',
        'commission',
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
