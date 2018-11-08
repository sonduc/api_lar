<?php

namespace App\Http\Controllers\ApiCustomer;

use App\Events\Customer_Register_Event;
use App\Http\Transformers\UserTransformer;
use App\Repositories\Users\UserRepository;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends ApiController
{
    protected $validationRules = [
        'email'                 => 'required|email|max:255|unique:users,email',
        'password'              => 'required|min:6|max:255',
        'password_confirmation' => 'required|min:6|max:255|same:password',
        'status'                => 'numeric |between:0,1|filled',
        'type'                  => 'numeric |between:0,1|filled',
    ];

    protected $validationMessages = [
        'email.required'                    => 'Vui lòng nhập email',
        'email.email'                       => 'Email không đúng định dạng',
        'email.max'                         => 'Email cần nhỏ hơn :max kí tự',
        'email.unique'                      => 'Email đã được sử dụng',
        'password.required'                 => 'Vui lòng nhập mật khẩu',
        'password.min'                      => 'Mật khẩu cần lớn hơn :min kí tự',
        'password.max'                      => 'Mật khẩu cần nhỏ hơn :max kí tự',
        'password_confirmation.required'    => 'Vui lòng nhập mật khẩu',
        'password_confirmation.min'         => 'Mật khẩu cần lớn hơn :min kí tự',
        'password_confirmation.max'         => 'Mật khẩu cần nhỏ hơn :max kí tự',
        'password_confirmation.same'        => 'Mật khẩu không khớp nhau',
        'type.between'                      => 'Mã không hợp lệ',
        'type.numeric'                      => 'Kiểu phải là kiểu số',
        'type.filled'                       => 'Vui lòng nhập kiểu tài khoản vào ',
        'status.numeric'                    => 'Mã kích hoạt tài khoản phải là kiểu số',
        'status.between'                    => 'Mã kích hoạt tài khoản không phù hợp',
        'status.filled'                     => 'Vui lòng nhập mã trạng thái ',

    ];

    public function __construct(UserRepository $user, UserTransformer $transformer)
    {
        $this->user = $user;
        $this->setTransformer($transformer);
    }

    public function register(Request $request)
    {

        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $params    = $request->all();
            $username  = $params['email'];
            $password  = $params['password'];

            // Create new user
            $newClient = $this->getResource()->store($params);

            // Issue token
            $guzzle  = new Guzzle;
            $url     = env('APP_URL') . '/oauth/token';
            $options = [
                'json'   => [
                    'grant_type'    => 'password',
                    'client_id'     => env('CLIENT_ID', 0),
                    'client_secret' => env('CLIENT_SECRET', ''),
                    'username'      => $username,
                    'password'      => $password,
                ],
                'verify' => false,
            ];
            $result  = $guzzle->request('POST', $url, $options)->getBody()->getContents();
            $result  = json_decode($result, true);

            event(new Customer_Register_Event($newClient));
            return $this->successResponse($result, false);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $clientException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors' => ['Tài khoản customer chưa được xác thực.'],
            ], $clientException->getCode());
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function getResource()
    {
        return $this->user;
    }

    /**
     * Update status cuả nguời dùng khi xác nhận email
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function confirm(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->user->checkUserByStatus($request->all());
            if (!empty($user)) throw new \Exception('Tài khoản đã được kích hoạt');
            $validate = array_only($this->validationRules, [
                'status',
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->user->updateStatus($request->all());
            DB::commit();
            return $this->successResponse($data);
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
