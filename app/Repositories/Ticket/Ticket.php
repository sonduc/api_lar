<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 3:44 PM
 */

namespace App\Repositories\Ticket;


use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Entity
{
    use PresentationTrait,SoftDeletes;
    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;


    const RESOLVE_STATUS    = [
        self::AVAILABLE      => 'CHƯA GIẢI QUYẾT',
        self::UNAVAILABLE    => 'ĐÃ GIẢI QUYẾT',
    ];
    protected $table = 'tickets';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
           'title','content','topic_id','subtopic_id','supporter_id','user_create_id','resolve'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}