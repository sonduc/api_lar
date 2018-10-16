<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Categories\Category;
use Illuminate\Http\Request;

use App\Http\Transformers\CategoryTransformer;
use App\Repositories\Categories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends ApiController
{
    protected $validationRules = [
        'hot'                               => 'required|integer|between:0,1',
        'status'                            => 'required|integer|between:0,1',
        //'image'                         =>'image|mimes:jpeg,bmp,png,jpg',
        'details.*.*.name'                  => 'required|v_title|unique:categories_translate,name',
        'details.*.*.lang'                  => 'required',
    ];
    protected $validationMessages = [
        //type=status
        'status.required'                   => 'Vui lòng chọn trạng thái',
        'status.integer'                    => 'Mã trạng thái phải là kiểu số',
        'status.between'                    => 'Mã trạng thái không phù hợp',
        //type=hot
        'hot.required'                      => 'Vui lòng chọn mã nổi bật',
        'hot.integer'                       => 'Mã nổi bật phải là kiểu số',
        'hot.between'                       => 'Mã nổi bật không phù hợp',

        //'image.image'                   =>'Định dạng không phải là hình ảnh',
       // 'image.mimes'                   => 'Hình ảnh phải thuộc kiểu jpg,bmp,jpeg,png',
        'details.*.*.name.required'         => 'Tên không được để trống',
        'details.*.*.name.unique'           => 'Tên này đã tồn tại',
        'details.*.*.name.v_title'          => 'Tên danh mục không hợp lệ',
        'details.*.*.lang.required'         => 'Vui lòng chọn ngôn ngữ',
    ];

    /**
     * CategoryController constructor.
     * @param CategoryRepository $catagory
     */
    public function __construct(CategoryRepository $catagory)
    {
        $this->model = $catagory;
        $this->setTransformer(new CategoryTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
        return $this->successResponse($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            //$this->authorize('category.view');
            $trashed = $request->has('trashed') ? true : false;
            $data = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('category.create');
            $data= $request->except('image');
            $data['image'] = rand_name($request->image);
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($data);
            DB::commit();
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        $this->validationRules['details.*.*.name']       = "required|v_title";
        try {
            $this->authorize('category.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());
            DB::commit();
            // dd(DB::getQueryLog());
            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
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
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->model->deleteCategory($id);
            logs('category', 'xóa danh mục mã ' . $id);
            //dd(DB::getQueryLog());
            DB::commit();
            return $this->deleteResponse();
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
     * Thực hiện cập nhật hot, status cho danh mục
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

            $avaiable_option = ['hot', 'status'];
            $option          = $request->get('option');

            if (!in_array($option, $avaiable_option)) throw new \Exception('Không có quyền sửa đổi mục này');
            $validate = array_only($this->validationRules, [
                $option,
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->singleUpdate($id, $request->only($option));
            logs('categories', 'sửa trạng thái của danh mục có code ' . $data->code, $data);
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

    /**
     * Lấy ra các Trạng thái danh mục (theo status)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function statusList()
    {
        try {
            $data = $this->simpleArrayToObject(Category::CATEGORY_STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Lấy ra các Trạng thái danh mục (theo hot)
     * @author 0ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function hotList()
    {
        try {
            $data = $this->simpleArrayToObject(Category::CATEGORY_HOT);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
