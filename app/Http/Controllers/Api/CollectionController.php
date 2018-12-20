<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Collections\Collection;
use App\Repositories\Collections\CollectionLogic;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

use App\Http\Transformers\CollectionTransformer;
use App\Repositories\Collections\CollectionRepository;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Exception\ImageException;

class CollectionController extends ApiController
{
    protected $validationRules = [
        'hot'                               => 'integer|between:0,1|filled',
        'status'                            => 'integer|between:0,1|filled',
        'new'                               => 'integer|between:0,1|filled',
        //'image'                             =>'image|mimes:jpeg,bmp,png,jpg',
        'details.*.*.name'                  => 'required|v_title|unique:collection_translates,name',
        'details.*.*.description'           => 'required',
        'details.*.*.lang'                  => 'required',
        'rooms.0'                           => 'required',
        'rooms.*'                           => 'required|integer|exists:rooms,id|distinct',
    ];

    protected $validationMessages = [
        'hot.integer'                       => 'Mã nổi bật không phù hợp',
        'hot.between'                       => 'Mã nổi bật không phù hợp',
        'hot.filled'                        => 'Vui lòng nhập mã trạng thái ',
        'status.integer'                    => "Mã trạng thái phải là kiểu số",
        'status.between'                    => "Mã trạg thái phải là kiểu số 0 hoặc 1",
        'status.filled'                      => 'Vui lòng nhập mã trạng thái ',
        'new.integer'                       => "Mã sưu tập mới phải là kiểu số",
        'new.between'                       => "Mã sưu tập mới phải là kiểu số 0 hoặc 1",
        'new.filled'                        => 'Vui lòng nhập mã trạng thái ',
        'details.*.*.name.required'         => 'Tên bộ sưu tập không được để trông',
        'details.*.*.name.v_title'          => 'Tên bộ sưu tập không hợp lệ',
        'details.*.*.description.required'  => 'Tên mô tả bộ sưu tập không được để trống',
        'details.*.*.name.unique'           => 'Tên bộ sưu tập này đã tồn tại',
        'details.*.*.lang.required'         => 'Mã ngôn ngữ này không được để trống',
        'rooms.*.1.required'                => 'Trường này không được để trống',
        'rooms.*.required'                  => 'Trường này không được để trống',
        'rooms.*.integer'                   => 'Mã phòng phải là kiểu số',
        'rooms.*.exists'                    => 'Mã phòng không tồn tại trong hệ thống',
        'rooms.*.distinct'                  => 'Mã phòng không được phép trùng nhau',



    ];

    /**
     * CollectionController constructor.
     * @param CollectionRepository $collection
     */
    public function __construct(CollectionLogic $collection)
    {
        $this->model = $collection;
        $this->setTransformer(new CollectionTransformer);
    }

    /**
     * Display a listing of the resource.
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        try{
            $this->authorize('collection.view');
            $pageSize = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            return $this->successResponse($data);
        }catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     *  Display a listing of the resource.
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('collection.view');
            $trashed = $request->has('trashed') ? true : false;
            $data = $this->model->getById($id, $trashed);
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
     * Thêm mới bộ sưu tập
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwabl
     * e
     */

    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('collection.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('collection', 'tạo bộ sưu tập mã ' . $data->id, $data);
            return $this->successResponse($data, true, 'details');
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
        } catch (ImageException $imageException) {
            return $this->notSupportedMediaResponse([
                'error' => $imageException->getMessage(),
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
     * Cập nhập bộ sưu tập
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
            $this->authorize('collection.update');
            $this->validationRules['details.*.*.name'] = "required|v_title";
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('collection', 'sửa bộ sưu tập' . $data->id, $data);
            return $this->successResponse($data, true, 'details');
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function destroy($id)
    {
        try {
            $this->authorize('collection.delete');
            $this->model->destroyColection($id);

            return $this->deleteResponse();
        } catch (AuthorizationException $f) {
            DB::rollBack();
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
     * Thực hiện cập nhật hot, status,new
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function singleUpdate(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('collection.update');
            $avaiable_option = ['hot', 'status','new'];
            $option          = $request->get('option');
            if (!in_array($option, $avaiable_option)) {
                throw new \Exception('Không có quyền sửa đổi mục này');
            }

            $validate = array_only($this->validationRules, [
                $option,
            ]);

            $this->validate($request, $validate, $this->validationMessages);

            $data = $this->model->singleUpdate($id, $request->only($option));
            logs('blogs', 'sửa trạng thái của bộ sưu tập có code ' . $data->code, $data);
            DB::commit();
            return $this->successResponse($data);
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
     * Lấy ra các Trạng thái bài viết (theo status)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function statusList()
    {
        try {
            $this->authorize('collection.view');
            $data = $this->simpleArrayToObject(Collection::COLLECTION_STATUS);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Lấy ra các Trạng thái bài viết (theo hot)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function hotList()
    {
        try {
            $this->authorize('collection.view');
            $data = $this->simpleArrayToObject(Collection::COLLECTION_HOT);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Lấy ra các Trạng thái bài viết (theo new)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function newList()
    {
        try {
            $this->authorize('collection.view');
            $data = $this->simpleArrayToObject(Collection::COLLECTION_NEW);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
