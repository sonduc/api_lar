<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\UserTransformer;
use App\Repositories\Users\UserRepository;
use App\User;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends ApiController
{
    protected $validationRules = [
        'name'     => 'required|min:2|max:255',
        'phone'    => 'nullable|digits_between:8,12|unique:users,phone',
        'email'    => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:6|max:255',
        'type'     => 'required|between:0,1',
    ];

    protected $validationMessages = [
        'name.required'     => 'Vui lòng nhập tên',
        'name.min'          => 'Tên cần lớn hơn :min kí tự',
        'name.max'          => 'Tên cần nhỏ hơn :max kí tự',
        'phone.required'    => 'Vui lòng nhập số điện thoại',
        'phone.min'         => 'Số điện thoại cần lớn hơn :min kí tự',
        'phone.max'         => 'Số điện thoại cần nhỏ hơn :max kí tự',
        'phone.unique'      => 'Số điện thoại đã được sử dụng',
        'email.required'    => 'Vui lòng nhập email',
        'email.email'       => 'Email không đúng định dạng',
        'email.max'         => 'Email cần nhỏ hơn :max kí tự',
        'email.unique'      => 'Email đã được sử dụng',
        'password.required' => 'Vui lòng nhập mật khẩu',
        'password.min'      => 'Mật khẩu cần lớn hơn :min kí tự',
        'password.max'      => 'Mật khẩu cần nhỏ hơn :max kí tự',
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
            $hasher = app()->make('hash');

            $params = $request->all();
            $username  = $params['email'];
            $password  = $params['password'];


            $type_user = array_get($params, 'type', User::USER);

            // Create new user
            $newClient = $this->getResource()->store($params);

            // Khởi tạo quyền đối với đối tác cung cấp
//            if($type_user == User::MERCHANT) {
//                $newClient->roles()->attach([app()->make(\App\Repositories\Roles\RoleRepository::class)->getBySlug('merchant')->id]);
//            }

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
                'errors' => ['Tài khoản developer chưa được xác thực.'],
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
}
