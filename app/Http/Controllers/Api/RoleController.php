<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\RoleTransformer;
use App\Repositories\Roles\RoleRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends ApiController
{
    protected $validationRules
        = [
            'name'        => 'required|unique:roles,name',
            'slug'        => 'required',
            'permissions' => 'nullable|array',
        ];
    protected $validationMessages
        = [
            'name.required'     => 'Tên không được để trông',
            'slug.required'     => 'Slug không được để trông',
            'permissions.array' => 'Danh sách quyền không đúng định dạng array',
        ];

    /**
     * RoleController constructor.
     *
     * @param RoleRepository $role
     */
    public function __construct(RoleRepository $role)
    {
        $this->model = $role;
        $this->setTransformer(new RoleTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('role.view');
            $pageSize = $request->get('limit', 25);
            return $this->successResponse($this->model->getByQuery($request->all(), $pageSize));
        } catch (AuthorizationException $f) {
            return $this->forbidden([
               'error' => $f->getMessage(),
           ]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('role.view');
            return $this->successResponse($this->model->getById($id));
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('role.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());

            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->authorize('role.update');
            $validate = array_only($this->validationRules, [
                'permissions',
                'slug'
            ]);

            $this->validate($request, $validate, $this->validationMessages);
            $model = $this->model->update($id, $request->all());

            return $this->successResponse($model);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function destroy($id)
    {
        try {
            $this->authorize('role.delete');
            $this->model->delete($id);

            return $this->deleteResponse();
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function updateMerchantRole()
    {
        $list_merchant = DB::table('users')->where('type', 1)->pluck('id');
        foreach ($list_merchant as $k => $uid) {
            DB::table('role_users')->insert([
                'user_id' => $uid,
                'role_id' => 3
                ]);
        }
    }

    public function updateImage()
    {
        $list_image = DB::table('room_medias')->get();
        foreach ($list_image as $room) {
            // dd($room);
            if ($room->id < 90000) {
                $current_img = $room->image;
                // dd($room->image);
                if (strpos($current_img, ".png")) {
                    $test = str_replace(".png", "", $current_img);
                } elseif (strpos($current_img, ".jpeg")) {
                    $test = str_replace(".jpeg", "", $current_img);
                } else {
                    $test = $current_img;
                }
                // dump($test);
                DB::table('room_medias')->where('id', $room->id)->update([
                        'image' => $test
                    ]);
            }
        }
    }
}
