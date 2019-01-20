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
     * @param null $data
     * @param array $except
     * @param array $only
     * @return \App\Repositories\Eloquent
     */
    public function minorUpdate($id, $data = null, $except = [], $only = [])
    {
        return parent::update($id,$data);
    }


}