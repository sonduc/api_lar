<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/16/2019
 * Time: 3:46 PM
 */

namespace App\Http\Controllers\Api;

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
            'name'                                      => 'required|v_title',
            'description'                               => 'required',
            'address'                                   => 'required|min:5',

            'bank_account'                              => 'array|required',
            'bank_account.*.account_name'               => 'required|min:3|v_title|distinct',
            'bank_account.*.account_number'             => 'required|min:3|v_title|distinct',
            'bank_account.*.bank_branch'                => 'required|min:3|v_title',


            //'homepage_image'                          =>'required/image|mimes:jpeg,bmp,png,jpg',
            //'image_logo'                              =>'required/image|mimes:jpeg,bmp,png,jpg',

            'contact_email'                             => 'array|required',
            'contact_email.*.email'                     =>'required|email|max:100|distinct',
            'contact_email.*.status'                    =>'integer|between:0,1',

            'contact_hotline'                           => 'array|required',
            'contact_hotline.*.phone'                   =>'required|max:20|distinct|regex:/^\+?[0-9-]*$/',
            'contact_hotline.*.status'                  =>'integer|between:0,1',

            'meta_title'                                => 'required',
            'meta_description'                          => 'required',

            'meta_keywords'                             => 'array|required|distinct',
            'meta_keywords.*'                           => 'distinct',
        ];
    protected $validationMessages
        = [
            'name.required'                             => 'Trường này không được để trống',
            'name.v_title'                              => 'Tên không hợp lệ',
            'description.required'                      => 'Trường này không được để trống',
            'address.required'                          => 'Trường này không được để trống',

            'bank_account.array'                        => "Dữ liệu phải là dạng mảng",
            'bank_account.required'                     => "Trường này không được để trống",

            'bank_account.*.account_name.required'      => 'Trường này không được để trống',
            'bank_account.*.account_name.min'           => 'Tên tài khoản phải ít nhất 3 ký tự',
            'bank_account.*.account_name.v_title'       => 'Tên tài khoản không howpk lệ',
            'bank_account.*.account_name.distinct'      => 'Tên tài khoản không được trùng nhau',

            'bank_account.*.account_number.required'    => 'Số tài khoản không được để trống',
            'bank_account.*.account_number.min'         => 'Số tài khoản phải ít nhất 3 ký tự',
            'bank_account.*.account_number.v_title'     => 'Số tài khoản không hợ lê',
            'bank_account.*.account_number.distinct'    => 'Số tài khoản không được trùng nhau',

            'bank_account.*.bank_branch.required'       => 'Trường này không được để trống',
            'bank_account.*.bank_branch.min'            => 'Chi nhánh ngân hàng phải ít nhất 3 ký tự',
            'bank_account.*.bank_branch.v_title'        => 'Tên chi nhánh ngân hàng không hợp lệ',


            //'homepage_image.image'                    =>'Định dạng không phải là hình ảnh',
            //'homepage_image.required'                 =>'Định dạng không phải là hình ảnh',
            //'homepage_image.mimes'                    => 'Hình ảnh phải thuộc kiểu jpg,bmp,jpeg,png',
            //'image_logo.image'                        =>'Định dạng không phải là hình ảnh',
            //'image_logo.required'                     =>'Định dạng không phải là hình ảnh',
            //'image_logo.mimes'                        => 'Hình ảnh phải thuộc kiểu jpg,bmp,jpeg,png',

            'contact_email.array'                       => "Dữ liệu phải là dạng mảng",
            'contact_email.required'                    => "Trường này không được để trống",

            'contact_email.*.email.required'            => 'Trường này không được để trống',
            'contact_email.*.email.email'               => 'Không đúng định dạng email',
            'contact_email.*.email.max'                 => 'Độ dài email không hợp lệ',
            'contact_email.*.email.distinct'            => 'Email không được trùng nhau',

            'contact_hotline.array'                     => "Dữ liệu phải là dạng mảng",
            'contact_hotline.required'                  => "Trường này không được để trống",

            'contact_hotline.*.phone.required'          => 'Trường này không được để trống',
            'contact_hotline.*.phone.max'               => 'Mã trạng thái phải là kiểu số',
            'contact_hotline.*.phone.regex'             => 'Số điện thoại không hợp lệ',
            'contact_hotline.*.phone.distinct'          => 'Số điện thoại không được trùng nhau',


            'meta_title.required'                       => "Trường này không để trống",
            'meta_description.required'                 => "Trường này không để trống",
            'meta_keywords.array'                       => "Dữ liệu phải là dạng mảng",
            'meta_keywords.required'                    => "Trường này không được để trống",

            'meta_keywords.*.distinct'                  => "Các keyword không thể trùng nhau"


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
            $this->authorize('settingMain.view');
            $data        = $this->model->getAll();
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     * Tạo mới Settings.
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('settingMain.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);


            $model = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('setting-main', 'taọ setting cho hệ thống' . $model->id, $model);
            return $this->successResponse($model);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
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

    /**
     * Cập nhập settings
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('settingMain.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->updateSettings($id,$request->all());
            //dd(DB::getQueryLog());
            DB::commit();
            logs('setting-main', 'Cập nhật setting cho hệ thống mã' . $model->id, $model);
            //dd(DB::getQueryLog());
            return $this->successResponse($model);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Xóa Settings
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function destroy($id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('settingMain.delete');
            $this->model->delete($id);
            DB::commit();
            //dd(DB::getQueryLog());
            return $this->deleteResponse();
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
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

    /**
     *Update setting liên hệ bằng email
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateContact(Request $request,$id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('settingMain.update');
            $avaiable_option = [
                'contact_hotline',
                'contact_email',

            ];
            $option = $request->get('option');
            if (!in_array($option, $avaiable_option)) {
                throw new \Exception('Không có quyền sửa đổi mục này');
            }

            $validate = array_only($this->validationRules, [
                $option,
            ]);

            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->updateSettings($id, $request->only($option));
            DB::commit();
            logs('settings', 'sửa settings mã ' . $data->id, $data);
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

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function settingStatus()
    {
        try {
            $data = $this->simpleArrayToObject(Setting::SETTING_STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

}