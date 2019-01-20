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

    /**
     * Lấy ticket theo id
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return \App\Repositories\Eloquent
     */
    public function getTicketById($data = [])
    {
         return parent::getById($data['ticket_id']);
    }


    /**
     * Lấy tất cả các ticket của người tạo nó
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $params
     * @param $size
     * @return mixed
     * @throws \ReflectionException
     */
    public function getTicketByUserId($id, $params, $size)
    {
        $this->useScope($params);
        return $this->model
            ->where('tickets.user_create_id', $id)
            ->paginate($size);
    }

}