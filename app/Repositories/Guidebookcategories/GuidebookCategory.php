<?php

namespace App\Repositories\GuidebookCategories;

use App\Repositories\Places\Place;
use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuidebookCategory extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','icon','lang'
    ];

    protected $table = 'guidebook_category';

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * relation ship voi place
     * @author sonduc <ndson1998@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function places()
    {
        return $this->hasMany(Place::class, 'guidebook_category_id', 'id');
    }
}
