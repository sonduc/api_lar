<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/20/2019
 * Time: 5:12 PM
 */

namespace App\Repositories\Ticket;

use Illuminate\Support\Facades\Auth;

trait TicketLogicTrait
{
    protected $model;
    protected $commentTicket;


    public function store($data = null)
    {
        $data['user_create_id'] = Auth::user()->id;
        $data['rosolve']        = Ticket::UNAVAILABLE;
        $data_ticket            = parent::store($data);
        return $data_ticket;

    }

    public function update($id, $data = null, $except = [], $only = [])
    {
        $user_id        = Auth::user()->id;
        $ticket         = parent::getById($id);
        if ($ticket->resolve ==  Ticket::AVAILABLE)
        {
            throw new \Exception('Thẻ Ticket này đã đóng nên bạn không thẻ bình luận thêm được');

        }

        if ($user_id == $ticket->user_create_id )
        {
            $data                   = array_except($data,$except);
            $data_ticket            = parent::update($id,$data);
            return $data_ticket;

        }else
        {
            throw new \Exception('Bạn không có quyền chỉnh sửa  thẻ này');
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
        $user_id = Auth::user()->id;
        $ticket         = parent::getById($id);

        if ($user_id == $ticket->user_create_id)
        {
            return parent::destroy($id);
        }else
        {
            throw new \Exception('Bạn không có quyền xóa bình luận ở thẻ này');
        }
    }


}