<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\User;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'roles',
        'parent',
        'child',
        'pers',
        'sale',
        'bookings',
        'rooms',
        'city',
        'district',
        'blog',
    ];

    /**
     * Các thông tin của user
     * @return array
     */
    public function transform(User $user = null)
    {
        if (is_null($user)) {
            return [];
        }

        return [
            'id'         => $user->id,
            'uuid'       => $user->uuid,
            'name'       => $user->name,
            'email'      => $user->email,
            'gender'     => $user->gender,
            'gender_txt' => $user->getGender(),
            'birthday'   => $user->birthday,
            'address'    => $user->address,
            'phone'      => $user->phone ?? 'Không xác định',
            'avatar'     => $user->avatar,
            'avatar_url' => app('url')->asset('images/default_avatar.png'),
            'level'      => $user->level,
            'level_txt'  => $user->getLevelStatus(),
            'vip'        => $user->vip ?? 0,
            'vip_txt'    => $user->getVipStatus() ?? 'Không xác định',
            'point'      => $user->point,
            'money'      => $user->money,
            'status'     => $user->status,
            'status_txt' => $user->getStatus(),
            'type'       => $user->type,
            'type_txt'   => $user->getAccountType(),
        ];
    }

    /**
     * Thêm các thông tin về chức vụ
     * @return array
     */
    public function includeRoles(User $user = null, ParamBag $params = null)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $user->roles())->get();

        return $this->collection($data, new RoleTransformer);
    }

    /**
     * Thêm các thông tin về quyền
     *
     * @param  User|null $user [description]
     *
     * @return [type]          [description]
     */

    public function includePers(User $user = null)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $pers = [];
        foreach ($user->roles as $value) {
            $pers[] = $this->displayPermission($value->permissions);
        }
        return $this->primitive($pers);
    }

    /**
     * Hiển thị quyền
     *
     * @param  [type] $permissions [description]
     *
     * @return array
     */
    private function displayPermission($permissions)
    {
        $allPermissions = array_collapse(array_map(function ($permission, $key) {
            $pers = [];
            foreach ($permission['list'] as $k => $v) {
                $pers [] = [
                    'group' => $permission['title'],
                    'slug'  => "${key}.${k}",
                    'name'  => $v,
                ];
            }
            return $pers;
        }, config('permissions'), array_keys(config('permissions'))));

        return array_values(array_where($allPermissions, function ($permission) use ($permissions) {
            return in_array($permission['slug'], array_keys($permissions));
        }));
    }

    /**
     * Xem tài khoản này được quản lý bởi tài khoản nào
     *
     * @param  User|null $user [description]
     *
     * @return object
     */
    public function includeParent(User $user = null)
    {
        if (is_null($user)) {
            return $this->null();
        }
        return $this->item($user->parent, new UserTransformer);
    }

    public function includeSale(User $user = null)
    {
        if (is_null($user->sale)) {
            return $this->null();
        }
        return $this->item($user->sale, new UserTransformer);
    }

    /**
     * Danh sách các user đang được quản lý bởi tài khoản này
     *
     * @param  User|null $user [description]
     *
     * @return array
     */
    public function includeChild(User $user = null, ParamBag $params)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $user->children())->get();

        return $this->collection($data, new UserTransformer);
    }

    public function includeBookings(User $user = null, ParamBag $params)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $user->bookings())->get();

        return $this->collection($data, new BookingTransformer);
    }

    public function includeCity(User $user = null)
    {
        if (is_null($user)) {
            return $this->null();
        }

        return $this->item($user->city, new CityTransformer);
    }

    public function includeDistrict(User $user = null)
    {
        if (is_null($user)) {
            return $this->null();
        }
        return $this->item($user->district, new DistrictTransformer());
    }
}
