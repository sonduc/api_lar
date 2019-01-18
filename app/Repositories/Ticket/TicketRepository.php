<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 3:44 PM
 */

namespace App\Repositories\Ticket;


use App\Repositories\BaseRepository;

class TicketRepository extends BaseRepository implements TicketRepositoryInterface
{
    /**
     * @var Ticket
     */
    protected $model;


    /**
     * TicketRepository constructor.
     * @param Ticket $ticket
     */
    public function __construct(
        Ticket $ticket
    )
    {
        $this->model         = $ticket;
    }

}