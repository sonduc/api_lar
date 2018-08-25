<?php

namespace App\Repositories\Comforts;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComfortTranslate extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'lang_id', 'comfort_id', 'description'
    ];


    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Relationship với ngôn ngữ
     * @return Relation
     */
    public function language()
    {
        return $this->belongsTo(\App\Repositories\Languages\Language::class, 'lang_id');
    }
}
