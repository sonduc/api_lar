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
    protected $table = 'comment_tickets';

    protected $fillable
        = [
            'comments','ticket_id','user_id'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
    
}