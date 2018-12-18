<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 17:38
 */

namespace App\Http\Controllers\ApiMerchant;


use App\Http\Controllers\ApiController;
use App\Events\Reset_Password_Event;
use Illuminate\Http\Request;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends ApiController
{
    protected $user;
    protected $validationRules
        = [
            'email' => 'required|email|max:255',
        ];

    protected $validationMessages
        = [
            'email.required' => 'Vui lòng nhập email',
            'email.email'    => 'Nhập đúng định dạng email',
            'email.max'      => 'Email cần nhỏ hơn :max kí tự',
        ];


    public function __construct(
        UserRepositoryInterface $user
    ) {
        $this->user           = $user;
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function forgetPassword(Request $request)
    {
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $user = $this->user->getUserByEmailOrPhone($request->all());
            if (!$user)
            {
                throw new \Exception('Tài khoản không tồn tại trên hệ thống');
            }

            // Cập nhâp token mỗi khi gửi mail
            $data['token'] = Hash::make( str_random(60));
            $user = $this->user->update($user->id, $data);
            event(new Reset_Password_Event($user));
            return $this->successResponse(['data' => ['message' => 'Đường dẫn đổi mật khẩu đã được gửi đến'.$request->email]], false);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $clientException) {
            return $this->errorResponse([
                'errors' => ['Tài khoản developer chưa được xác thực.'],
            ], $clientException->getCode());
        } catch (\Exception $e) {
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }


}
