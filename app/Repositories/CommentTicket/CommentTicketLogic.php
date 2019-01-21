<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/19/2019
 * Time: 9:51 PM
 */

namespace App\Repositories\CommentTicket;


use App\Repositories\BaseLogic;
use App\Repositories\Ticket\Ticket;
use App\Repositories\Ticket\TicketRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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