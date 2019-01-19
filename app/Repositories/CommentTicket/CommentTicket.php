<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/19/2019
 * Time: 11:11 AM
 */

namespace App\Repositories\CommentTicket;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\Entity;

class CommentTicket extends Entity
{
    use SoftDeletes;
    protected $fillable
        = [
            'comments','ticket_id','user_id'
        ];

}