<?php

namespace App\Repositories\Users;

use App\Repositories\BaseRepository;
use App\Repositories\Roles\Role;
use App\Services\Amazon\S3\ImageProcessor;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Bookings\BookingConstant;
use App\Events\AmazonS3_Upload_Event;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use FilterTrait, PresentationTrait;
    /**
     * User model.
     * @var Model
     */
    protected $model;
    protected $imgProc;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user, ImageProcessor $processor)
    {
        $this->model = $user;
        $this->imgProc = $processor;
    }

    /**
     * Lưu thông tin 1 bản ghi mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $user  = parent::store($data);
        $roles = array_get($data, 'roles', []);

        if (count($roles)) {
            $user->roles()->attach($roles);
        }

        return $user;
    }

    public function update($id, $data, $except = [], $only = [])
    {
        $user = parent::update($id, $data);
        $roles = array_get($data, 'roles', []);
        $user->roles()->detach();
        $user->roles()->attach($roles);
        return $user;
    }


    /**
     * Cập nhập thông tin cho customer
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $except
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */
    public function updateInfoCustomer($id, $data, $except = [], $only = [])
    {
        $data = array_only($data, $only);

        if (!isset($data['subcribe']) && empty($data['subcribe'])) {
            $data['subcribe'] = 1;
        }


        if (isset($data['settings'])) {
            $data['settings'] = json_encode($data['settings']);
        }

        if (isset($data['avatar']) && !empty($data['avatar'])) {
            $name                 = rand_name();
            $this->imgProc->setImage($name, $data['avatar']);
            event(new AmazonS3_Upload_Event($name, $data['avatar']));
            $data['avatar']       = $name;
        }

        $user = parent::update($id, $data);
        return $user;
    }


    /**
     * Cập nhật ảnh avatar
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $except
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */
    public function updateAvatar($id, $data, $except = [], $only = [])
    {
        $data = array_only($data, $only);

        if (isset($data['avatar']) && !empty($data['avatar'])) {
            $name                 = rand_name();
            $this->imgProc->setImage($name, $data['avatar']);
            event(new AmazonS3_Upload_Event($name, $data['avatar']));
            $data['avatar']       = $name;
        }

        $user = parent::update($id, $data);
        return $user;
    }

    /**
     *  Cập nhập settings cho customer
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param       $id
     * @param       $data
     * @param array $except
     * @param array $only
     * @param array $list
     *
     * @return \App\Repositories\Eloquent
     */
    public function updateSettingCustomer($id, $data, $except = [], $only = [], $list = [])
    {
        $data = array_only($data, $only);

        if (empty($data['subcribe'])) {
            $data['subcribe'] = 1;
        }
        if (isset($data['settings']) && !empty($data['settings'])) {
            foreach ($data['settings'] as $k => $val) {
                if (empty($val)) {
                    $list[$k] = User::DISABLE;
                } else {
                    $list[$k] = $data['settings'][$k];
                }
            }
        }

        $data['settings'] = json_encode($list);
        $user             = parent::update($id, $data);
        return $user;
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $user
     * @param $request
     *
     * @throws \Exception
     */
    public function checkValidPassword($user, $request)
    {
        if (!Hash::check($request['old_password'], $user->password)) {
            throw new \Exception('Mật khẩu không chính xác');
        } elseif ($request['old_password'] === $request['password']) {
            throw new \Exception('Mật khẩu không được trùng với mật khẩu cũ');
        }
    }

    /**
     * Lấy dữ liệu về giới tính
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed
     */
    public function getSexConstant()
    {
        return $this->model::SEX;
    }

    /**
     * Lấy thông tin về cấp độ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed
     */
    public function getLevelConstant()
    {
        return $this->model::LEVEL;
    }

    /**
     * Lấy thông tin về loại tài khoản
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed
     */
    public function getAccountTypeConstant()
    {
        return $this->model::TYPE_ACCOUNT;
    }

    /**
     * Lấy thông tin user qua email hoặc SĐT
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return array
     */
    public function getUserByEmailOrPhone($data = [])
    {
        $email = array_key_exists('email', $data) ? $data['email'] : 'Không xác định';
        $phone = array_key_exists('phone', $data) ? $data['phone'] : 'Không xác định';
        $data  = $this->model->where('email', $email)->orWhere('phone', $phone)->first();
        return $data;
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return mixed
     */
    public function checkEmailOrPhone($data = [])
    {
        $email = array_key_exists('email', $data) ? $data['email'] : 'Không xác định';
        $phone = array_key_exists('phone', $data) ? $data['phone'] : 'Không xác định';
//        dd($data);
        return $this->model
            ->where([
                'type_create' => User::BOOKING,
                'email'       => $email,
                'status'      => User::DISABLE,
            ])
            ->orWhere(function ($query) use ($phone) {
                return $query->where([
                    'type_create' => User::BOOKING,
                    'phone'       => $phone,
                    'status'      => User::DISABLE,
                ]);
            })
            ->first();
    }

    /**
     *   Kiểm tra user có tồn tại hay không hoặc đã ở trạng thái đã kích hoạt chưa
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     *
     * @return mixed
     */
    public function checkUser($uuid)
    {
        $user = $this->model->where('uuid', $uuid)->first();
        if (empty($user)) {
            throw  new \Exception('Đường dẫn không tồn tại');
        }

        if (!empty($user) & $user->status == User::ENABLE) {
            throw new \Exception('Tài khoản đã được kích hoạt');
        }

        return $user;
    }

    /**
     * Update mã kích hoạt tài hoản cho người dùng
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */

    public function updateStatus($user)
    {
        $data['status'] = User::ENABLE;
        return parent::update($user->id, $data);
    }



    public function getUserOwner($params)
    {
        if (isset($params['owner']) == true && is_numeric($params['owner'])) {
            $query = $this->model
                ->select('name', 'email')
                ->where('owner', $params['owner'])
                ->where('email', '<>', null)
                ->groupBy('email');
            if (isset($params['city']) == true && is_numeric($params['city'])) {
                $query->where('city_id', $params['city']);
            }
        } else {
            $query = $this->model
                ->select('name', 'email')
                ->where('email', '<>', null)
                ->groupBy('email');
        }
        return $query->get();
    }

    /**
     * Get users by list id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $idUsers
     * @param $params
     *
     * @return array
     */
    public function getUserByListIdIndex($idUsers, $params): array
    {
        /** @var Collection $getVal */
        // dd($idUsers);
        // dd($idUsers, $params, $this->model->whereIn('id', $idUsers)->where('owner', $params)->toSql());
        $getVal = $this->model->whereIn('id', $idUsers)->where('owner', $params)->get(['id', 'name']);

        return $getVal->map(function ($value) {
            return [
                'id'   => $value->id,
                'name' => $value->name,
            ];
        })->toArray();
    }

    /**
     * reset lại mật khẩu theo uuid và cập nhập lại token cho user
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     *
     * @return \App\Repositories\Eloquent|mixed
     */
    public function resetPasswordCustomer($user, $data)
    {
        $data['password']                = $data['password'];
        $data['token']                   = Hash::make(str_random(60));
        $data['status']                  = User::ENABLE;

        // Câp nhât lại trạng thái gửi gửi mail của user.
        $data['limit_send_mail']         = User::NO_LIMIT_SEND_MAIL;
        $data['count_send_mail']         = 0;
        return parent::update($user->id, $data);
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     *
     * @throws \Exception
     */

    public function checkValidToken($data)
    {
        $user  = $this->model->where('token', $data['token'])->first();
        if (empty($user)) {
            throw new \Exception('Đường dẫn không tồn tại');
        }
        return $user;
    }


    /**
     * Kiểm tra thời gian tồn tại của đường link xác nhận mật khẩu
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $code
     *
     * @throws \Exception
     */
    public function checkTime($user, $code, $data = [])
    {
        $timeNow    = Carbon::now();
        $timeSubmit = base64_decode($code);
        $timeSubmit = Carbon::createFromTimestamp($timeSubmit)->toDateTimeString();
        $minutes    = $timeNow->diffInMinutes($timeSubmit);
        // Nếu sao 24 h khách hàng không phản hồi thì đường dẫn bị hủy đồng thời Câp nhât lại trạng thái gửi gửi mail của user.
        if ($minutes > 1440) {
            $data['limit_send_mail'] = User::NO_LIMIT_SEND_MAIL;
            $data['count_send_mail'] =  0;
            parent::update($user->id, $data);
            if (!empty($user)) {
                throw new \Exception('Đường dẫn không tồn tại ');
            }
        }
    }

    /**
     * Kiểm tra xem Referral code có hợp lệ không
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param $ref
     *
     * @throws \Exception
     */

    public function isValidReferenceID($ref)
    {
        if ($this->model->select('id')->where('uuid', $ref)->first() !== null) {
            return true;
        }

        return false;
    }

    public function getIDbyUUID($uuid)
    {
        return $this->model->select('id')->where('uuid', $uuid)->first()->id;
    }

    public function countBookingCustomer($uid)
    {
        $count_booking_complete = $this->model->where('status', BookingConstant::BOOKING_COMPLETE)->where('customer_id', $uid)->count();
    }

    public function getListUserById($listUser, $owner)
    {
        return $this->model->whereIn('id', $listUser)->where('owner', $owner)->get();
    }


    /**
     *  Kiểm tra xem user này có những quyền gì
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param bool $trash
     * @param bool $useHash
     * @return mixed
     */
    public function checkValidRole($id, $trash = false, $useHash = false)
    {
        $user =  parent::getById($id, $trash, $useHash); // TODO: Change the autogenerated stub
        return $user->roles->toArray();
    }

    /**
     * Lấy tất cả các user có quyền supporter
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return mixed
     */
    public function getUserByRoleSupporter()
    {
        return  $this->model->select('users.id', 'users.name')
                    ->join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->where('role_users.role_id', '=', Role::SUPPORTER)->get();
    }

    /**
     * Lấy dữ liệu user theo id của nó
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return \App\Repositories\Eloquent
     */

    public function getUserById($id)
    {
        return parent::getById($id);

    }
}
