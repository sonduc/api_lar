<?php

namespace App\Repositories\CompareCheckings;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompareChecking extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    public $table = 'compare_checking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'total_debit',
        'total_credit',
        'total_bonus',
        'total_compare_checking',
        'user_id',
        'status'
    ];
    
    const PENDING = 0;
    const DONE    = 1;

    const STATUS = [
        self::PENDING => 'Đang chờ đối soát',
        self::DONE    => 'Đã đối soát'
    ];


    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
