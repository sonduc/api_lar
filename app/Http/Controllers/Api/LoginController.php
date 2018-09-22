<?php

namespace App\Http\Controllers\Api;

use App\User;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Http\Request;

class LoginController extends ApiController
{
    protected $validationRules
        = [
            'username' => 'required|email|max:255',
            'password' => 'required|min:5|max:255',
        ];
    
    protected $validationMessages
        = [
            'username.required' => 'Vui lòng nhập email',
            'username.email'    => 'Nhập đúng định dạng email',
            'username.max'      => 'Email cần nhỏ hơn :max kí tự',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min'      => 'Mật khẩu cần lớn hơn :min kí tự',
            'password.max'      => 'Mật khẩu cần nhỏ hơn :max kí tự',
        ];
    
    /**
     * Function processing login account
     *
     * @param  Request $request [description]
     *
     * @return [type]           [description]
     */
    public function login(Request $request)
    {
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $hasher   = app()->make('hash');
            $username = $request->input('username');
            $password = $request->input('password');
            $login    = User::where('email', $username)->first();
            if (!$login) {
                return $this->errorResponse([
                    'errors' => ['Thông tin đăng nhập không chính xác.'],
                ]);
            } else {
                if ($hasher->check($password, $login->password)) {
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
                    logs('user', $login->name . ' đăng nhập');
                    return $this->successResponse($result, false);
                }
                return $this->errorResponse([
                    'errors' => ['Thông tin đăng nhập không chính xác.'],
                ]);
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
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
