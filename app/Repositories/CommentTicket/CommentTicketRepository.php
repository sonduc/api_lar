<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/19/2019
 * Time: 11:11 AM
 */

namespace App\Repositories\CommentTicket;


use App\Repositories\BaseRepository;

use Illuminate\Support\Facades\Auth;
class CommentTicketRepository extends BaseRepository implements CommentTicketRepositoryInterafae
{
    protected $model;

    /**
     * CommentTicketRepository constructor.
     * @param CommentTicket $commentTicket
     */
    public function __construct(CommentTicket $commentTicket)
    {
        $this->model = $commentTicket;
    }

    /**
     * Lấy comment ticket theo id
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return \App\Repositories\Eloquent
     */
    public function getCommentTicketById($id)
    {
        return parent::getById($id);
    }


}