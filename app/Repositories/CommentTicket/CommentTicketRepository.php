<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:12 PM
 */

namespace App\Repositories\CommentTicket;


use App\Repositories\BaseRepository;

class CommentTicketRepository extends BaseRepository implements CommentTicketRepositoryInterface
{

    /**
     * @var CommentTicket
     */
    protected $model;

    /**
     * CommentTicketRepository constructor.
     * @param CommentTicket $commentTicket
     */
    public function __construct(CommentTicket $commentTicket)
    {
        $this->model = $commentTicket;
    }

}