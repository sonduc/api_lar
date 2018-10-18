<?php

namespace App;

use App\Repositories\Entity;
use App\Repositories\Users\FilterTrait;
use App\Repositories\Users\PresentationTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

// use Illuminate\Database\Eloquent\Model;

class User extends Entity implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens, FilterTrait, PresentationTrait, SoftDeletes;

    const ENABLE   = 1;
    const DISABLE  = 0;
    const ADMIN    = 2;
    const MERCHANT = 1;

    // Định nghĩa loại tài khoản
    const USER   = 0;
    const MALE   = 1;
    const FEMALE = 2;

    // Định nghĩa giới tính
    const OTHER  = 3;
    const NONE   = 0;
    const SEX    = [
        self::MALE   => 'Nam',
        self::FEMALE => 'Nữ',
        self::OTHER  => 'Khác',
        self::NONE   => 'Không xác định',
    ];
    const BROZE  = 0;
    const SILVER = 1;
    // Định nghĩa cấp độ
    const GOLD         = 2;
    const PLATINUM     = 3;
    const DIAMOND      = 4;
    const LEVEL        = [
        self::BROZE    => 'Đồng',
        self::SILVER   => 'Bạc',
        self::GOLD     => 'Vàng',
        self::PLATINUM => 'Bạch Kim',
        self::DIAMOND  => 'Kim Cương',
    ];
    const VIP_ACTIVE   = 1;
    const VIP_DEACTIVE = 0;

    // Định nghĩa VIP
    const TYPE_ACCOUNT = [
        self::ADMIN    => 'Quản trị hệ  thống',
        self::MERCHANT => 'Đối tác cung cấp',
        self::USER     => 'Người sử dụng',
    ];

    const IS_OWNER  = 1;
    const NOT_OWNER = 0;
    const OWNER     = [
        self::IS_OWNER  => 'Người quản lý',
        self::NOT_OWNER => 'Bình thường',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'gender',
        'phone',
        'birthday',
        'sub_email',
        'avatar',
        'address',
        'owner',
        'facebook_id',
        'google_id',
        'level',
        'point',
        'money',
        'passport_last_name',
        'passport_first_name',
        'passport_infomation',
        'passport_front_card',
        'passport_back_card',
        'city_id',
        'district_id',
        'type',
        'status',
        'sale_id',
        'account_number',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password',];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->uuid = hashid_encode($model->id);
            $model->save();
        });


    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = app('hash')->make($value);
    }

    /**
     * Relationship with Role
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Repositories\Roles\Role::class, 'role_users');
    }

    public function parent()
    {
        return $this->belongsTo(\App\User::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(\App\User::class, 'parent_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(\App\User::class, 'sale_id');
    }

    public function bookings()
    {
        return $this->hasMany(\App\Repositories\Bookings\Booking::class, 'customer_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Repositories\Cities\City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(\App\Repositories\Districts\District::class, 'district_id');
    }


    public function validateForPassportPasswordGrant($password)
    {
        if ($password == $this->password || app('hash')->check($password, $this->password)) {
            return true;
        }

        return false;
    }
}
