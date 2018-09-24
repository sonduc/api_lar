<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Roles\Role;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes
        = [
            'users',
            'pers',
        ];

    public function transform(Role $role = null)
    {
        if (is_null($role)) {
            return [];
        }

        return [
            'id'          => $role->id,
            'name'        => $role->name,
            'slug'        => $role->slug,
            'permissions' => $role->permissions,
            'admin_only'  => $role->admin_only,
        ];
    }

    public function includeUsers(Role $role = null, ParamBag $params = null)
    {
        if (is_null($role)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $role->users())->get();
        return $this->collection($data, new UserTransformer);
    }

    public function includePers(Role $role = null)
    {
        if (is_null($role)) {
            return $this->null();
        }
        return $this->primitive($this->displayPermission($role->permissions));
    }

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
}
