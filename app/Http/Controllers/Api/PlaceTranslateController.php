<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\PlaceTranslateTransformer;
use App\Repositories\PlaceTranslates\PlaceTranslate;
use App\Repositories\PlaceTranslates\PlaceTranslateLogic;
use App\Repositories\PlaceTranslates\PlaceTranslateRepository;
use DB;
use Illuminate\Http\Request;

class PlaceTranslateController extends ApiController
{
    protected $validationRules = [
        'name'                 => 'required|v_title',
        'description'          => 'required',
        'lang'                 => 'required|v_title',
        'place_id'             => 'required|integer|exists:places,id,deleted_at,NULL',
    ];
    protected $validationMessages = [
        'name.required'        => 'Vui lòng điền tên',
        'name.v_title'         => 'Tên không đúng định dạng',
        'name.unique'          => 'Tên dịch địa điểm này đã tồn tại',
        'description.required' => 'Mô tả không được để trống',
        'lang.required'        => 'Vui lòng chọn định dạng ngôn ngữ',
        'lang.v_title'         => 'Định dạng ngôn ngữ không hợp lệ',
        'place_id.required'    => 'Địa điểm không được để trống',
        'place_id.integer'     => 'Mã địa điểm phải là kiểu số',
        'place_id.exists'      => 'Địa điểm không tồn tại',
    ];

    /**
     * PlaceTranslateController constructor.
     * @param PlaceTranslateRepository $placetranslate
     */
    public function __construct(PlaceTranslateLogic $placetranslate)
    {
        $this->model = $placetranslate;
        $this->setTransformer(new PlaceTranslateTransformer);
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
        $this->authorize('place.view');
        $pageSize    = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
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
            $this->authorize('place.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
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
            $this->authorize('place.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
            DB::commit();
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
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
            $this->authorize('place.update');

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $model = $this->model->update($id, $request->all());

            return $this->successResponse($model);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
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
            $this->authorize('place.delete');
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
