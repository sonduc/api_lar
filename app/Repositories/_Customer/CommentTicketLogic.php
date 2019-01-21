<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/20/2019
 * Time: 11:41 AM
 */

namespace App\Repositories\_Customer;


use App\Repositories\BaseLogic;
use App\Repositories\CommentTicket\CommentTicketLogicTrait;
use App\Repositories\CommentTicket\CommentTicketRepositoryInterafae;
use App\Repositories\Ticket\TicketRepositoryInterface;


class CommentTicketLogic extends BaseLogic
{
    use CommentTicketLogicTrait;
    protected $model;
    protected $ticket;
    public function __construct(
        CommentTicketRepositoryInterafae $commentTicket,
        TicketRepositoryInterface $ticket
    ) {
        $this->model             = $commentTicket;
        $this->ticket            = $ticket;
    }

}