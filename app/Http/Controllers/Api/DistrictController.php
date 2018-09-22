<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\DistrictTransformer;
use App\Repositories\Districts\DistrictRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictController extends ApiController
{
    protected $validationRules
        = [
            'name'       => 'required|v_title|unique:districts,name',
            'short_name' => 'required|string|size:5|unique:districts,short_name',
            'code'       => 'required|size:6|unique:districts,code',
            'priority'   => 'numeric|between:0,3',
            'hot'        => 'numeric|between:0,1',
            'status'     => 'numeric|between:0,1',
        ];
    protected $validationMessages
        = [
            'name.required'       => 'Vui lòng điền tên quận/huyện',
            'name.v_title'        => 'Tên quận/huyện không hợp lệ',
            'name.unique'         => 'Tên quận/huyện đã tồn tại',
            'short_name.required' => 'Vui lòng thêm tên ngắn',
            'short_name.string'   => 'Tên ngắn phải là kiểu chữ',
            'short_name.size'     => 'Tên ngắn phải dài 5 ký tự',
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
     * DistrictController constructor.
     *
     * @param DistrictRepository $district
     */
    public function __construct(DistrictRepository $district)
    {
        $this->model = $district;
        $this->setTransformer(new DistrictTransformer);
        
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('district.view');
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
            $this->authorize('district.view');
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
            $this->authorize('district.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            // return $request->all();
            $data = $this->model->store($request->all());
            DB::commit();
            logs('district', 'thêm tỉnh mã ' . $data->id, $data);
            return $this->successResponse($data);
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
    
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('district.update');
            $this->validationRules['name']       .= ",{$id}";
            $this->validationRules['short_name'] .= ",{$id}";
            $this->validationRules['code']       .= ",{$id}";
            
            $this->validate($request, $this->validationRules, $this->validationMessages);
            
            $data = $this->model->update($id, $request->all());
            DB::commit();
            logs('district', 'sửa tỉnh mã ' . $data->id, $data);
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
            $this->authorize('district.delete');
            $data = $this->model->delete($id);
            DB::commit();
            logs('district', 'xóa tỉnh mã ' . $id, $data);
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
