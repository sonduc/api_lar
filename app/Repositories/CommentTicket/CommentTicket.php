<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:12 PM
 */

namespace App\Repositories\CommentTicket;


use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentTicket extends Entity
{
    use SoftDeletes;
    protected $fillable
        = [
            'comments','ticket_id','user_id'
        ];
}