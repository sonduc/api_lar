<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Users\UserRepository;
use Illuminate\Http\Request;
use App\Http\Transformers\UserTransformer;

class ProfileController extends ApiController
{
    protected $validationRules = [
        'name'  => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ];
    protected $validationMessages = [
        'name.required'  => 'Tên không được để trông',
        'email.required' => 'Email không được để trông',
        'email.email'    => 'Email không đúng định dạng',
        'email.unique'   => 'Email đã tồn tại trên hệ thống',
        'password.required' => 'Mật khẩu không được để trống',
        'password.min' => 'Mật khẩu phải có ít nhât :min ký tự',
        'password.confirmed' => 'Nhập lại mật khẩu không đúng',
    ];
    /**
     * UserController constructor.
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
        return $this->successResponse($request->user());
    }

    public function update(Request $request)
    {
        $this->validationRules['email'] .= ',' . $request->user()->id;
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->update($request->user()->id, $request->all(), [], ['name', 'phone']);

            return $this->successResponse($model);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function changePassword(Request $request)
    {
        $this->validationRules = array_only($this->validationRules, ['password']);

        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->update($request->user()->id, $request->all(), [], ['password']);

            return $this->successResponse(['data' => ['message' => 'Đổi mật khẩu thành công']], false);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
