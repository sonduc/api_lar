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
            'id'        => $user->id,
            'name'      => $user->name,
            'joined_in' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : trans2(ErrorCore::UNDEFINED),
        ];
    }

}
