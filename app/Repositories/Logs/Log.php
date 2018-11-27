<?php

namespace App\Repositories\Logs;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    const LOG_NAME
        = [
            'room' => 'Phòng',
            'user' => 'Người dùng',
        ];
    protected $table = 'activity_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [

        ];
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'causer_id');
    }
}
