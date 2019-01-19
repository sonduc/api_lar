<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:32 PM
 */

namespace App\Http\Transformers;


use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Ticket\Ticket;
use League\Fractal\TransformerAbstract;
class TicketTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Ticket $ticket= null)
    {
        if (is_null($ticket)) {
            return [];
        }

        return [
            'id'                => $ticket->id,
            'subtopic_id'       => $ticket->subtopic_id ?? "Không xác định",
            'topic_id'          => $ticket->topic_id,
            'user_create_id'    => $ticket->user_create_id,
            'title'             => $ticket->title,
            'content'           => $ticket->content,
            'resolve'           => $ticket->resolve ?? 0,
            'resolve_txtx'      => $ticket->managerStatus(),
            'created_at'        => $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'        => $ticket->updated_at ? $ticket->updated_at->format('Y-m-d H:i:s') : null


        ];
    }

}