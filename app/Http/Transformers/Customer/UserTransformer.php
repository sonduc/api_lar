<?php

namespace App\Http\Transformers\Customer;

use App\Helpers\ErrorCore;
use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

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
                 'phone'          => "",
                 'phone_txt'      => $user->phone ?? 'Không xác định',
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
                 'subcribe'       => $user->subcribe,
                 'settings'       =>  json_decode($user->settings),

             ];
//            'joined_in' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : trans2(ErrorCore::UNDEFINED),
    }

}
