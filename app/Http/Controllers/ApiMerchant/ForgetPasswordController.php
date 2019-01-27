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
use App\Repositories\Roles\Role;
use Illuminate\Http\Request;
use App\Repositories\Users\UserRepositoryInterface;
use App\User;
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
            if (!empty($user)) {
                // Nếu đã trang thái hạn chế gửi mail tồn tại (=1)thì gửi cho user tối đa 5 maijl khi đường đãn còn tồn tại
                //Nếu vượt quá 5 mail thì chỉ đua ra thông báo // cho user kiểm tra lại hòm mail

                if ($user['limit_send_mail'] == User::LIMIT_SEND_MAIL) {
                    if ($user['count_send_mail'] == User::MAX_COUNT_SEND_MAIL) {
                        return $this->successResponse(['data' => ['message' => 'Bạn hãy vui lòng check mail để thiết lập mật khẩu...']], false);
                    }

                    $data['limit_send_mail'] = User::LIMIT_SEND_MAIL;
                    $data['count_send_mail'] =  $user['count_send_mail'] +1;
                    $data['token']           = Hash::make(str_random(60));
                    $user                    =$this->user->update($user->id, $data);

                    //Tạo quyền lại cho merchant khi thiết lập lại mật khẩu
                    $user->roles()->attach([Role::MERCHANT]);

                    event(new Reset_Password_Event($user));
                    return $this->successResponse(['data' => ['message' => 'Đường dẫn đổi mật khẩu đã được gửi đến'.$request->email]], false);
                }
                // Nêu chưa tồn tại trạng thái hạn chế gửi mail(=null) thì gửi mail cho user và cập nhập chơ hạn chế gửi mail cho user này
                $data['limit_send_mail'] = User::LIMIT_SEND_MAIL;
                $data['count_send_mail'] =  $user['count_send_mail'] +1;
                $data['token']           = Hash::make(str_random(60));
                $user                    = $this->user->update($user->id, $data);

                //Tạo quyền lại cho merchant khi thiết lập lại mật khẩu
                $user->roles()->attach([Role::MERCHANT]);

                event(new Reset_Password_Event($user));


                return $this->successResponse(['data' => ['message' => 'Đường dẫn đổi mật khẩu đã được gửi đến'.$request->email]], false);
            } else {
                throw new \Exception('Tài khoản không tồn tại trên hệ thống');
            }
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
