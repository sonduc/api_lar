<?php

namespace App\Repositories\Places;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\GuidebookCategories\GuidebookCategory;

class Place extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    // Định nghĩa trạng thái place
    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;
    const PLACE_STATUS    = [
        self::AVAILABLE      => 'KHẢ DỤNG',
        self::UNAVAILABLE    => 'KHÔNG KHẢ DỤNG',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description','guidebook_category_id','latitude','longitude','status'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * ralation ship với GuidebookCategory
     * @author sonduc <ndson1998@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guidebookcategory()
    {
        return $this->belongsTo(GuidebookCategory::class, 'guidebook_category_id', 'id');
    }

    /**
     *
     * @author sonduc <ndson1998@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function rooms()
    {
        return $this->belongsToMany(\App\Repositories\Rooms\Room::class, 'room_places', 'place_id', 'room_id');
    }
}
