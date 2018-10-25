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
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     *
     * @return array
     */
    public function transform(User $user = null)
    {
        if (is_null($user)) {
            return [];
        }

        return [
            'id'             => $user->id,
            'uuid'           => $user->uuid,
            'name'           => $user->name,
            'email'          => $user->email,
            'gender'         => $user->gender,
            'gender_txt'     => $user->getGender(),
            'birthday'       => $user->birthday,
            'address'        => $user->address,
            'phone'          => $user->phone ?? 'Không xác định',
            'account_number' => $user->account_number,
            'avatar'         => $user->avatar,
            'avatar_url'     => app('url')->asset('images/default_avatar.png'),
            'level'          => $user->level,
            'level_txt'      => $user->getLevelStatus(),
            'vip'            => $user->vip ?? 0,
            'vip_txt'        => $user->getVipStatus() ?? 'Không xác định',
            'point'          => $user->point,
            'money'          => $user->money,
            'status'         => $user->status,
            'status_txt'     => $user->getStatus(),
            'type'           => $user->type,
            'type_txt'       => $user->getAccountType(),
        ];
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null     $user
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeRoles(User $user = null, ParamBag $params = null)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $data = $this->pagination($params, $user->roles());

        return $this->collection($data, new RoleTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     *
     * @return \League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
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
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $permissions
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

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeSale(User $user = null)
    {
        if (is_null($user->sale)) {
            return $this->null();
        }
        return $this->item($user->sale, new UserTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     * @param ParamBag  $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeChild(User $user = null, ParamBag $params)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $user->children())->get();

        return $this->collection($data, new UserTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     * @param ParamBag  $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeBookings(User $user = null, ParamBag $params)
    {
        if (is_null($user)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $user->bookings())->get();

        return $this->collection($data, new BookingTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeCity(User $user = null)
    {
        if (is_null($user)) {
            return $this->null();
        }

        return $this->item($user->city, new CityTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param User|null $user
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeDistrict(User $user = null)
    {
        if (is_null($user)) {
            return $this->null();
        }
        return $this->item($user->district, new DistrictTransformer());
    }
}
