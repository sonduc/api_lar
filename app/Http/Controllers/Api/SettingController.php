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

        ];
    protected $validationMessages
        = [

        ];

    /**
     * BlogController constructor.
     *
     * @param BlogRepository $blog
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