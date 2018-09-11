<?php

namespace App\Repositories\Bookings;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Bookings\BookingConstant;
class Booking extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'code', 'name', 'phone', 'email','sex', 'birthday', 'email_received', 'name_received', 'phone_received', 'room_id', 'customer_id', 'merchant_id', 'checkin', 'checkout', 'price_original', 'price_discount', 'coupon', 'note', 'total_fee', 'status', 'number_of_guests', 'service_fee', 'type', 'booking_type', 'payment_method', 'payment_status', 'price_range', 'source', 'exchange_rate'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            $model->uuid = hashid_encode($model->id);
            $model->code = strtoupper(BookingConstant::PREFIX.hashid_encode($model->id));
            $model->save();
        });
    }

    public function customer()
    {
        return $this->belongsTo(\App\User::class, 'customer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(\App\User::class, 'merchant_id');
    }

    public function bookingStatus()
    {
        return $this->hasOne(\App\Repositories\Bookings\BookingStatus::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Repositories\Payments\PaymentHistory::class, 'booking_id');
    }
}
