<?php

namespace App\Repositories\Users;

use App\Repositories\BaseRepository;
use App\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use FilterTrait, PresentationTrait;
    /**
     * User model.
     * @var Model
     */
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
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
        $email = array_key_exists('email', $data) ? $data['email'] : null;
        $phone = array_key_exists('phone', $data) ? $data['phone'] : null;
        $data  = $this->model->where('email', $email)->orWhere('phone', $phone)->first();

        return $data;
    }
}
