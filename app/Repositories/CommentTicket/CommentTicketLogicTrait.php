<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/20/2019
 * Time: 5:19 PM
 */

namespace App\Repositories\CommentTicket;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Ticket\Ticket;

trait CommentTicketLogicTrait
{
    protected $model;
    protected $ticket;

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param null $data
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function store($data = null)
    {
        $data['user_id']         = Auth::user()->id;
        $ticket = $this->ticket->getTicketById($data);
        if ($ticket->resolve ==  Ticket::AVAILABLE)
        {
            throw new \Exception('Thẻ Ticket này đã đóng nên bạn không thẻ bình luận thêm được');

        }
        if ($data['user_id'] == $ticket->supporter_id || $data['user_id'] == $ticket->user_create_id)
        {
            return parent::store($data);
        }else
        {
            throw new \Exception('Bạn không có quyền bình luận ở thẻ này');
        }

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
     * @throws \Exception
     */
    public function update($id, $data = null, $except = [], $only = [])
    {
        $user_id        = Auth::user()->id;
        $comment_ticket = $this->model->getCommentTicketById($id);
        $ticket         = $this->ticket->getTicketById($comment_ticket);
        if ($ticket->resolve ==  Ticket::AVAILABLE)
        {
            throw new \Exception('Thẻ Ticket này đã đóng nên bạn không thẻ bình luận thêm được');

        }

        if ($user_id == $comment_ticket->user_id )
        {
            return parent::update($id,$data);

        }else
        {
            throw new \Exception('Bạn không có quyền chỉnh sửa bình luận ở thẻ này');
        }

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
     * @throws \Exception
     */
    public function delete($id, $data = null, $except = [], $only = [])
    {
        $user_id        = Auth::user()->id;
        $comment_ticket = $this->model->getCommentTicketById($id);

        if ($user_id == $comment_ticket->user_id)
        {
            return parent::destroy($id);
        }else
        {
            throw new \Exception('Bạn không có quyền xóa bình luận ở thẻ này');
        }
    }

}