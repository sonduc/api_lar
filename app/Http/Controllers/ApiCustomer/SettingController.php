<?php

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Transformers\SettingTransformers;
use App\Repositories\Settings\Setting;
use App\Repositories\Settings\SettingRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends ApiController
{
    protected $validationRules
        = [
            'name'                                  => 'required',
            'description'                           => 'required',
            'address'                               => 'required|min:5',
            'bank_account.*.account_number'         => 'required|min:3|regex:/^\+?[0-9-]*$/',
            'homepage_image'                        =>'required',
            'image_logo'                            =>'required',
            'contact_email.*.email'                 =>'required|email|max:100|distinct',
            'contact_email.*.status'                =>'integer|between:0,1',
            'contact_hotline.*.phone'               =>'required|max:20|distinct|regex:/^\+?[0-9-]*$/',
            'contact_hotline.*.status'              =>'integer|between:0,1',
        ];
    protected $validationMessages
        = [
            'name.required'                         => 'Trường này không được để trống',
            'description.required'                  => 'Trường này không được để trống',
            'address.required'                      => 'Trường này không được để trống',
            'bank_account.*.account_number.required'  => 'Trường này không được để trống',
            'bank_account.*.account_number.min'       => 'Số tài khoản ngân hàng phải nhiều hơn 3 chữ số',
            'bank_account.*.account_number.regex'     => 'Số tài khoản ngân hàng không hợp lệ',


            'homepage_image.required'               => 'Ảnh trang chủ là bắt buộc',
            'image_logo.required'                   => 'Ảnh logo là bắt buộc',
            'contact_email.*.email.required'        => 'Trường này không được để trống',
            'contact_email.*.email.email'           => 'Không đúng định dạng email',
            'contact_email.*.email.max'             => 'Độ dài email không hợp lệ',
            'contact_email.*.email.distinct'        => 'Email không được trùng nhau',
            'contact_email.*.status.between'        => 'Mã trạng thái không hợp lệ',

            'contact_hotline.*.phone.required'      => 'Trường này không được để trống',
            'contact_hotline.*.phone.max'           => 'Mã trạng thái phải là kiểu số',
            'contact_hotline.*.phone.regex'         => 'Số điện thoại không hợp lệ',
            'contact_hotline.*.phone.distinct'      => 'Số điện thoại không được trùng nhau',
            'contact_hotline.*.status.between'      => 'Mã trạng thái không hợp lệ',

        ];

    /**
     * SettingController constructor.
     * @param SettingRepositoryInterface $setting
     */
    public function __construct(SettingRepositoryInterface $setting)
    {
        $this->model = $setting;
        $this->setTransformer(new SettingTransformers);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        try {
            $data        = $this->model->getAll();
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }
}
