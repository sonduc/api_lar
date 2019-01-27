<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 17:37
 */

namespace App\Http\Controllers\ApiMerchant;

use App\Http\Controllers\ApiController;
use App\Repositories\Roles\Role;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ResetPasswordController extends  ApiController
{
    protected $user;
    protected $validationRules
        = [
            'password'                  => 'required|min:6|confirmed',
        ];

    protected $validationMessages
        = [
            'password.required'         => 'Mật khẩu không được để trống',
            'password.min'              => 'Mật khẩu phải có ít nhât  ký tự',
            'password.confirmed'        => 'Mật khẩu không trùng khớp',
        ];


    public function __construct(
        UserRepositoryInterface $user
    ) {
        $this->user           = $user;
    }

    public function getFormResetPassword()
    {
        return view('email.form_reset_password');

    }
    public function resetPassword(Request $request, $time)
    {
        try {
            // Kiểm tra token có trùng với token trong database user không
            $data= $request->only('token','password');
            $user = $this->user->checkValidToken($data);
            // Kiểm tra sự tồn tại của đường link
            $this->user->checkTime($user,$time);

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $user = $this->user->resetPasswordCustomer($user, $data);

            //Tạo quyền lại cho merchant khi thiết lập lại mật khẩu
            $user->roles()->attach([Role::MERCHANT]);
            logs('user', 'Khôi phục mật khẩu ' . $user->email , $user);

            return $this->successResponse(['data' => ['message' => 'Thành công !!! Cám ơn bạn đã sử dụng dịch vụ của WESTAY']], false);

        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

}
