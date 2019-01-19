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
            'id'                => $commentTicket->id,
            'subtopic_id'       => $commentTicket->ticket_id ?? "Không xác định",
            'topic_id'          => $commentTicket->user_id,
            'user_create_id'    => $commentTicket->comments,
            'created_at'        => $commentTicket->created_at ? $commentTicket->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'        => $commentTicket->updated_at ? $commentTicket->updated_at->format('Y-m-d H:i:s') : null

        ];
    }

}