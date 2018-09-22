<?php

namespace App\Repositories\Payments;

use App\Repositories\Entity;

class PaymentHistory extends Entity
{
    use PresentationTrait, FilterTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'booking_id', 'money_received', 'total_received', 'total_debt', 'note', 'status', 'confirm',
        ];
    
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
    
    
}
