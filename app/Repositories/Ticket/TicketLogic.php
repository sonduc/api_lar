<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/19/2019
 * Time: 12:38 AM
 */

namespace App\Repositories\Ticket;


use App\Repositories\BaseLogic;
use App\Repositories\CommentTicket\CommentTicketRepositoryInterafae;
use Illuminate\Support\Facades\Auth;


class TicketLogic extends BaseLogic
{
    protected $model;
    protected $commentTicket;
    public function __construct(
        TicketRepositoryInterface $ticket,
        CommentTicketRepositoryInterafae $commentTicket
    ) {
        $this->model         = $ticket;
        $this->commentTicket = $commentTicket;
    }


    public function store($data = null)
    {
        $data['user_create_id'] = Auth::user()->id;
        $data['rosolve']        = Ticket::UNAVAILABLE;
        $data_ticket            = parent::store($data);
        $this->commentTicket->storeCommentTicket($data_ticket,$data);
        return $data_ticket;

    }

    public function update($id, $data = null, $except = [], $only = [])
    {

    }

    public function updateResolve($id, $data = null, $except = [], $only = [])
    {
        return parent::update($id,$data);

    }


}