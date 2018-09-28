<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\UserTransformer;
use App\Repositories\Users\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    protected $validationRules
        = [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    protected $validationMessages
        = [
            'name.required'      => 'Tên không được để trông',
            'email.required'     => 'Email không được để trông',
            'email.email'        => 'Email không đúng định dạng',
            'email.unique'       => 'Email đã tồn tại trên hệ thống',
            'password.required'  => 'Mật khẩu không được để trống',
            'password.min'       => 'Mật khẩu phải có ít nhât :min ký tự',
            'password.confirmed' => 'Nhập lại mật khẩu không đúng',
        ];

    /**
     * UserController constructor.
     *
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->model = $user;
        $this->setTransformer(new UserTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        $this->authorize('user.view');
        $pageSize = $request->get('limit', 25);

        $this->trash = $this->trashStatus($request);
        $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
//        dd(DB::getQueryLog());
        return $this->successResponse($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('user.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
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
        DB::beginTransaction();
        try {
            $this->authorize('user.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());
            DB::commit();
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function update(Request $request, $id)
    {
        $this->validationRules['email'] .= ',' . $id;

        unset($this->validationRules['password']);
        DB::beginTransaction();
        try {
            $this->authorize('user.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->update($id, $request->all());
            DB::commit();
            return $this->successResponse($model);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('user.delete');
            $this->model->delete($id);
            DB::commit();
            return $this->deleteResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Danh sách giới tính
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function sexList()
    {
        try {
            return response()->json($this->model->getSexConstant());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Danh sách cấp độ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function levelList()
    {
        try {
            return response()->json($this->model->getLevelConstant());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Danh sách loại tài khoản
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function accountTypeList()
    {
        try {
            return response()->json($this->model->getAccountTypeConstant());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
