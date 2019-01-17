<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/16/2019
 * Time: 5:27 PM
 */

namespace App\Repositories\Seo;


use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seo extends Entity
{
    use SoftDeletes;
    // Định nghĩa trạng thái seettings
    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;


    const SEO_STATUS    = [
        self::AVAILABLE      => 'HIỂN THỊ',
        self::UNAVAILABLE    => 'ẨN',
    ];

    protected $table = 'seo';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'meta_title','meta_description','meta_keywords'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}