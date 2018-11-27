<?php

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomTranslate extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable
        = [
            'name', 'lang', 'room_id', 'slug_name', 'address', 'slug_address', 'note', 'space', 'description',
        ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['slug_name', 'name'];
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Set slug_name attribute
     *
     * @param [type] $value [description]
     */
    public function setSlugNameAttribute($value)
    {
        $this->attributes['slug_name'] = to_slug($value);
    }

    /**
     * Relationship với ngôn ngữ
     * @return Relation
     */
}
