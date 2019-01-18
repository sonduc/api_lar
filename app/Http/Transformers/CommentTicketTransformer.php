<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:33 PM
 */

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\CommentTicket\CommentTicket;
use League\Fractal\TransformerAbstract;
class CommentTicketTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(CommentTicket $commentTicket= null)
    {
        if (is_null($commentTicket)) {
            return [];
        }

        return [

        ];
    }

}