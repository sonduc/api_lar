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
     * Lưu các comment thuộc môt ticket nào đó
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data_ticket
     * @param array $data
     */
    public function storeCommentTicket($data_ticket= [], $data = [])
    {
        if (isset($data_ticket) && !empty($data_ticket))
        {
            $data['ticket_id']       = $data_ticket->id;
        }
        $data['user_id']         = Auth::user()->id;
        parent::store($data);
    }

    /**
     * Update các comment thuộc môt ticket nào đó
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data_ticket
     * @param array $data
     */
    public function updateCommentTicket($data_ticket= [], $data = [])
    {
        if (isset($data_ticket) && !empty($data_ticket))
        {
            $data['ticket_id']       = $data_ticket->id;
        }

        $data['user_id']         = Auth::user()->id;
        parent::update($data);
    }

}