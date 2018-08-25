<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Comforts\ComfortRepository;
use Illuminate\Http\Request;
use App\Http\Transformers\ComfortTransformer;

class ComfortController extends ApiController
{
    protected $validationRules = [
        // 'region_id'         => 'required|numeric|between:1,3',
        // 'name'              => 'required|v_title|unique:cities,name',
        // 'short_name'        => 'required|string|size:3|unique:cities,short_name',
        // 'code'              => 'required|size:6|unique:cities,code',
        // 'longitude'         => '',
        // 'latitude'          => '',
        // 'priority'          => 'numeric|between:0,3',
        // 'hot'               => 'numeric|between:0,1',
        // 'status'            => 'numeric|between:0,1',
    ];
    protected $validationMessages = [
        // 'region_id.required'    => 'Vui lòng thêm vùng miền',
        // 'region_id.numeric'     => 'Không đúng dịnh dạng',
        // 'region_id.between'     => 'Mã vùng miền phải từ 1 đến 3',
        // 'name.required'         => 'Vui lòng điền tên thành phố',
        // 'name.v_title'          => 'Tên thành phố không hợp lệ',
        // 'name.unique'           => 'Tên thành phố đã tồn tại',
        // 'short_name.required'   => 'Vui lòng thêm tên ngắn',
        // 'short_name.string'     => 'Tên ngắn phải là kiểu chữ',
        // 'short_name.size'       => 'Tên ngắn phải dài 3 ký tự',
        // 'short_name.unique'     => 'Tên ngắn đã tồn tại',
        // 'code.required'         => 'Vui lòng nhập mã',
        // 'code.size'             => 'Độ dài phải là 6',
        // 'code.unique'           => 'Mã này đã có sẵn',
        // 'priority.numeric'      => 'Phải là kiểu số',
        // 'priority.between'      => 'Khoảng từ 0 đến 3',
        // 'hot.numeric'           => 'Phải là kiểu số',
        // 'hot.between'           => 'Khoảng từ 0 đến 1',
        // 'status.numeric'        => 'Phải là kiểu số',
        // 'status.between'        => 'Khoảng từ 0 đến 1',
    ];

    /**
     * ComfortController constructor.
     * @param ComfortRepository $comfort
     */
    public function __construct(ComfortRepository $comfort)
    {
        $this->model = $comfort;
        $this->setTransformer(new ComfortTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('comfort.view');
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
            // $this->authorize('comfort.view');
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
        try {
            // $this->authorize('comfort.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            // return $request->all();
            $data = $this->model->store($request->all());

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
        try {
            // $this->authorize('comfort.update');
            $this->validationRules['name'] .= ",{$id}";
            $this->validationRules['short_name'] .= ",{$id}";
            $this->validationRules['code'] .= ",{$id}";

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $model = $this->model->update($id, $request->all());

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

    public function destroy($id)
    {
        try {
            // $this->authorize('comfort.delete');
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
