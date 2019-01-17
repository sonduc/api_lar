<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/16/2019
 * Time: 5:25 PM
 */

namespace App\Repositories\Settings;


use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Entity
{
    use SoftDeletes;
    // Định nghĩa trạng thái seettings
    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;


    const SETTING_STATUS    = [
        self::AVAILABLE      => 'HIỂN THỊ',
        self::UNAVAILABLE    => 'ẨN',
    ];

    protected $table = 'settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'name','address','homepage_image','bank_accout','image_logo','description',
            'contact_hotline','contact_email'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}