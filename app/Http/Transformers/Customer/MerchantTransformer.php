<?php

namespace App\Http\Transformers\Customer;

use App\Helpers\ErrorCore;
use App\User;
use League\Fractal\TransformerAbstract;

class MerchantTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'city',
        'district'
    ];

    public function transform(User $user = null)
    {
        if (is_null($user)) {
            return [];
        }
        return [
                 'id'             => $user->id,
                 'uuid'           => $user->uuid,
                 'name'           => $user->name ?? '',
                 'gender'         => $user->gender,
                 'gender_txt'     => $user->getGender(),
                 'birthday'       => $user->birthday ?? '1950-01-01',
                 'avatar'         => $user->avatar ?? '',
                 'avatar_url'     => app('url')->asset('images/default_avatar.png'),


             ];
//            'joined_in' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : trans2(ErrorCore::UNDEFINED),
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
