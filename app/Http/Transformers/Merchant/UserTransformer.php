<?php

namespace App\Http\Transformers\Merchant;

use App\Http\Transformers\Traits\FilterTrait;
use App\User;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\RoleTransformer;

class UserTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'roles',
        'city',
        'district'
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
            'status'         => $user->status,
            'status_txt'     => $user->getStatus(),
            'type'           => $user->type,
            'type_txt'       => $user->getAccountType(),
            'account_branch'        => $user->account_branch,
            'passport_front_card'   => $user->passport_front_card,
            'passport_back_card'    => $user->passport_back_card,
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
