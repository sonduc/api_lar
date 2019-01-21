<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/20/2019
 * Time: 11:44 AM
 */

namespace App\Repositories\_Merchant;


use App\Repositories\BaseLogic;
use App\Repositories\CommentTicket\CommentTicketRepositoryInterafae;
use App\Repositories\Ticket\Ticket;
use App\Repositories\Ticket\TicketLogicTrait;
use App\Repositories\Ticket\TicketRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TicketLogic extends BaseLogic
{
    use TicketLogicTrait;

    protected $model;
    protected $commentTicket;
    public function __construct(
        TicketRepositoryInterface $ticket,
        CommentTicketRepositoryInterafae $commentTicket
    ) {
        $this->model         = $ticket;
        $this->commentTicket = $commentTicket;
    }



    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param bool $trash
     * @param bool $useHash
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function getById($id, $trash = false, $useHash = false)
    {
        $user_id = Auth::user()->id;
        $ticket  = parent::getById($id);
        if ($user_id == $ticket->user_create_id)
        {
            return$ticket;
        }else
        {
            throw new \Exception('Bạn không có quyền xem thẻ ticket này');
        }
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $params
     * @param $pageSize
     * @return mixed
     */
    public function getTicket($id,$params, $pageSize)
    {
        $ticket = $this->model->getTicketByUserId($id,$params, $pageSize);
        return $ticket;
    }

}