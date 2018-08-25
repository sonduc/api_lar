<?php

namespace App\Http\Controllers\Api;

class PermissionController extends ApiController
{
    /**
     * Get all permission from config file
     * @return json response
     */
    public function index()
    {
        $permissions = config('permissions');
//        foreach ($permissions as $key => $role) {
//            if ($key !== 'admin') {
//                foreach ($role['list'] as $key_role => $per) {
//                    print_r($key.'.'.$key_role.'------'.ucfirst($key).'Policy');
//                    echo '<br>';
//                }
//            }
//        }
        return response()->json($permissions, 200);
    }
}
