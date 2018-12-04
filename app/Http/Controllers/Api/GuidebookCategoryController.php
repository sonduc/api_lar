<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\GuidebookCategories\GuidebookCategoryLogic;
use App\Repositories\GuidebookCategories\GuidebookCategory;

use App\Http\Transformers\GuidebookCategoryTransformer;
use App\Repositories\GuidebookCategories\GuidebookCategoryRepository;
use DB;

class GuidebookCategoryController extends ApiController
{
    protected $validationRules = [
        'name'                      =>  'required|v_title|unique:promotions,name',
        'icon'                      =>  'required',
        'lang'                      =>  'required|v_title',
    ];
    protected $validationMessages = [
        'name.required'             =>  'Vui lòng điền tên',
        'name.v_title'              =>  'Tên không đúng định dạng',
        'name.unique'               =>  'Tên danh mục hướng dẫn này đã tồn tại',
        'icon.required'             =>  'Vui lòng điền mục icon',
        'lang.required'             =>  'Vui lòng chọn định dạng ngôn ngữ',
        'lang.v_title'              =>  'Định dạng ngôn ngữ không hợp lệ',
    ];

    /**
     * GuidebookCategoryController constructor.
     * @param GuidebookCategoryRepository $guidebookcategory
     */
    public function __construct(GuidebookCategoryLogic $guidebookcategory)
    {
        $this->model = $guidebookcategory;
        $this->setTransformer(new GuidebookCategoryTransformer);
    }

    /**
     * Display a listing of the resource.
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->authorize('guidebookcategory.view');
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
        return $this->successResponse($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param         $id
     *
     * @return mixed
     * @throws Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('guidebookcategory.view');
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

    /**
     * Store a record into database
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return mixed
     * @throws Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authorize('guidebookcategory.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
            DB::commit();
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

    /**
     * Update a record
     *
     * @param Request $request
     * @param         $id
     *
     * @return mixed
     * @throws Throwable
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->authorize('guidebookcategory.update');

            $this->validationRules['name'] .= ',' . $id;

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $model = $this->model->update($id, $request->all());
            DB::commit();
            return $this->successResponse($model);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
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
     * Destroy a record
     *
     * @param $id
     *
     * @return mixed
     * @throws Throwable
     */
    public function destroy($id)
    {
        try {
            $this->authorize('guidebookcategory.delete');
            $this->model->delete($id);

            return $this->deleteResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
