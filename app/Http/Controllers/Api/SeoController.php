<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/16/2019
 * Time: 3:46 PM
 */

namespace App\Http\Controllers\Api;


use App\Http\Transformers\SeoTransformers;
use App\Repositories\Seo\SeoRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeoController extends ApiController
{
    protected $validationRules
        = [
            'meta_title'                                 => 'required',
            'meta_description'                           => 'required',
            'meta_keywords.*'                            => 'bail|distinct',
            'meta_keywords'                              => 'array|required',
        ];
    protected $validationMessages
        = [
            'meta_title.required'                        => "Trường này không để trống",
            'meta_description.required'                  => "Trường này không để trống",
            'meta_keywords.array'                        => "Dữ liệu phải là dạng mảng",
            'meta_keywords.required'                     => "Trường này không được để trống",
            'meta_keywords.*.distinct'                   => "Các keyword không thể trùng nhau"

        ];

    /**
     * BlogController constructor.
     *
     * @param BlogRepository $blog
     */
    public function __construct(SeoRepositoryInterface $seo)
    {
        $this->model = $seo;
        $this->setTransformer(new SeoTransformers);
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
            $this->authorize('seo.view');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('seo.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Tạo mới SEO
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
            $this->authorize('seo.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('seo', 'taọ seo' . $model->id, $model);
            return $this->successResponse($model, true, 'details');
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Cập nhập SEO
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
            $this->authorize('seo.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->update($id, $request->all());
            //dd(DB::getQueryLog());
            DB::commit();
            logs('setting_main', 'Cập nhật setting cho hệ thống mã' . $model->id, $model);
            //dd(DB::getQueryLog());
            return $this->successResponse($model, true, 'details');
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
     * Xóa SEO
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
            $this->authorize('setting_main.delete');
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

}