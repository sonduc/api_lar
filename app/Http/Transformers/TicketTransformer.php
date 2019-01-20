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
use League\Fractal\ParamBag;
class TicketTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'userCreate',
        'supporter',
        'commentTickets'

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
            'supporter_id'      => $ticket->supporter_id,
            'title'             => $ticket->title,
            'content'           => $ticket->content,
            'resolve'           => $ticket->resolve ?? 0,
            'resolve_txtx'      => $ticket->managerStatus(),
            'created_at'        => $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'        => $ticket->updated_at ? $ticket->updated_at->format('Y-m-d H:i:s') : null
        ];

    }

    public function includeuserCreate(Ticket $ticket= null)
    {
        if (is_null($ticket)) {
            return $this->null();
        }
        return $this->item($ticket->userCreate, new UserTransformer);
    }

    public function includeSupporter(Ticket $ticket= null)
    {
        if (is_null($ticket)) {
            return $this->null();
        }
        return $this->item($ticket->supporter, new UserTransformer);
    }

    public function includeCommentTickets(Ticket $ticket= null, ParamBag $params = null)
    {
        if (is_null($ticket)) {
            return $this->null();
        }
        $data = $this->pagination($params, $ticket->commentTickets());
        return $this->collection($data, new CommentTicketTransformer());
    }

}