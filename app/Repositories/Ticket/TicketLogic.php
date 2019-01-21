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
use App\Repositories\Roles\Role;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;



class TicketLogic extends BaseLogic
{
    use TicketLogicTrait;
    protected $model;
    protected $commentTicket;
    protected $user;


    public function __construct(
        TicketRepositoryInterface $ticket,
        CommentTicketRepositoryInterafae $commentTicket,
        UserRepositoryInterface $user
    ) {
        $this->model         = $ticket;
        $this->commentTicket = $commentTicket;
        $this->user          = $user;
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
        $user_id        = Auth::user()->id;
        $ticket         = parent::getById($id);
        if ($ticket->resolve ==  Ticket::AVAILABLE)
        {
            throw new \Exception('Thẻ Ticket này đã đóng nên bạn không thẻ bình luận thêm được');

        }

        if ($user_id == $ticket->user_create_id)
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
     * Check xem user này có quyền supporter hay không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     * @throws \Exception
     */
    public function checkValidSupporter($data)
    {
        $id     = $data['supporter_id'];
        $role   = $this->user->checkValidRole($id);

        $list =  array_map(function ($value){
            return $value['id'];

        },$role);

        // Những user nào có quyền supporter mấy được thêm vào ticket
        if (!in_array(Role::SUPPORTER,$list) )
        {
            throw new \Exception('Supporter không hợp lệ');
        }

    }


    /**
     * Lấy ra danh sách các supporter
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return mixed
     */
    public function getSupporter()
    {
       return $this->user->getUserByRoleSupporter();
    }


}