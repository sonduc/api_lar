<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\CityTransformer;
use App\Repositories\Cities\CityRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends ApiController
{
    protected $validationRules
        = [
            'region_id'  => 'required|numeric|between:1,3',
            'name'       => 'required|v_title|unique:cities,name',
            'short_name' => 'required|string|size:3|unique:cities,short_name',
            'code'       => 'required|size:6|unique:cities,code',
            'longitude'  => '',
            'latitude'   => '',
            'priority'   => 'numeric|between:0,3',
            'hot'        => 'numeric|between:0,1',
            'status'     => 'numeric|between:0,1',
        ];
    protected $validationMessages
        = [
            'region_id.required'  => 'Vui lòng thêm vùng miền',
            'region_id.numeric'   => 'Không đúng dịnh dạng',
            'region_id.between'   => 'Mã vùng miền phải từ 1 đến 3',
            'name.required'       => 'Vui lòng điền tên thành phố',
            'name.v_title'        => 'Tên thành phố không hợp lệ',
            'name.unique'         => 'Tên thành phố đã tồn tại',
            'short_name.required' => 'Vui lòng thêm tên ngắn',
            'short_name.string'   => 'Tên ngắn phải là kiểu chữ',
            'short_name.size'     => 'Tên ngắn phải dài 3 ký tự',
            'short_name.unique'   => 'Tên ngắn đã tồn tại',
            'code.required'       => 'Vui lòng nhập mã',
            'code.size'           => 'Độ dài phải là 6',
            'code.unique'         => 'Mã này đã có sẵn',
            'priority.numeric'    => 'Phải là kiểu số',
            'priority.between'    => 'Khoảng từ 0 đến 3',
            'hot.numeric'         => 'Phải là kiểu số',
            'hot.between'         => 'Khoảng từ 0 đến 1',
            'status.numeric'      => 'Phải là kiểu số',
            'status.between'      => 'Khoảng từ 0 đến 1',
        ];

    /**
     * CityController constructor.
     *
     * @param CityRepository $city
     */
    public function __construct(CityRepository $city)
    {
        $this->model = $city;
        $this->setTransformer(new CityTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('city.view');
        $pageSize    = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
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
            $this->authorize('city.view');
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authorize('city.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            // return $request->all();
            $data = $this->model->store($request->all());
            DB::commit();
            logs('city', 'thêm thành phố mã ' . $data->id, $data);
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
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

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('city.update');
            $this->validationRules['name']       .= ",{$id}";
            $this->validationRules['short_name'] .= ",{$id}";
            $this->validationRules['code']       .= ",{$id}";

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->update($id, $request->all());
            DB::commit();
            logs('city', 'sửa thành phố mã ' . $id, $data);
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('city.delete');
            $this->model->delete($id);
            DB::commit();
            logs('city', 'xóa thành phố mã ' . $id);
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
}
