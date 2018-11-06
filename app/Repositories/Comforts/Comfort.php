<?php

namespace App\Repositories\Comforts;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comfort extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable
        = [
            'icon',
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param string $lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comfortTrans($lang = 'vi')
    {
        return $this->hasMany(ComfortTranslate::class, 'comfort_id')->where('lang', $lang);
    }

    /**
     * relation ship voi rooms
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function rooms()
    {
        return $this->belongsToMany(\App\Repositories\Rooms\Room::class, 'room_comforts');
    }
}
