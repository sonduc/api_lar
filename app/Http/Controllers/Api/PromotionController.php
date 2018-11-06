<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Promotions\Promotion;
use App\Repositories\Promotions\PromotionLogic;
use Illuminate\Http\Request;

use App\Http\Transformers\PromotionTransformer;
use Illuminate\Support\Facades\DB;
use App\Repositories\Promotions\PromotionRepository;

class PromotionController extends ApiController
{
    protected $validationRules = [
        'name'                      =>  'required|v_title|unique:promotions,name',
        'description'               =>  'required',
        // 'image'                     =>  'image|mimes:jpeg,bmp,png,jpg',
        'date_start'                =>  'required|date|after:now',
        'date_end'                  =>  'required|date|after:date_start',
        'status'                    =>  'required|integer|between:0,1',
    ];
    protected $validationMessages = [
        'name.required'             =>  'Vui lòng điền tên',
        'name.v_title'              =>  'Tên không đúng định dạng',
        'name.unique'               =>  'Tên chương trình khuyến mãi này đã tồn tại',
        'description.required'      =>  'Mô tả không được để trống',
        'date_start.required'       =>  'Vui lòng nhập thời gian bắt đầu chương trình khuyến mãi',
        'date_start.date_format'    =>  'Ngày bắt đầu chương trình khuyến mãi phải có định dạng Y-m-d H:i:s',
        'date_start.after'          =>  'Thời gian bắt đầu chương trình khuyến mãi không được phép ở thời điểm quá khứ',
        'date_end.required'         =>  'Vui lòng nhập thời gian kết thúc chương trình khuyến mãi',
        'date_end.date_format'      =>  'Ngày kết thúc chương trình khuyến mãi phải có định dạng Y-m-d H:i:s',
        'date_end.after'            =>  'Thời gian kết thúc chương trình khuyến mãi phải sau thời gian bắt đầu chương trình khuyến mãi',
        'status.required'           =>  'Trạng thái không được bỏ trống',
        'status.integer'            =>  'Trạng thái không phải là kiểu số',
        'status.between'            =>  'Trạng thái không phù hợp',
    ];

    /**
     * PromotionController constructor.
     * @param PromotionRepository $promotion
     */
    public function __construct(PromotionLogic $promotion)
    {
        $this->model = $promotion;
        $this->setTransformer(new PromotionTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        $this->authorize('promotion.view');
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        // dd(DB::getQueryLog());
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
            $this->authorize('promotion.view');
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
        try {
            $this->authorize('promotion.create');
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

    public function update(Request $request, $id)
    {
        try {
            $this->authorize('promotion.update');

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
            $this->authorize('promotion.delete');
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

    /**
     * Thực hiện cập nhật status
     * @author sonduc <ndson1998@gmail.com>
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
            $this->authorize('promotion.update');
            $avaiable_option = ['status'];
            $option          = $request->get('option');
            if (!in_array($option, $avaiable_option)) {
                throw new \Exception('Không có quyền sửa đổi mục này');
            }

            $validate = array_only($this->validationRules, [
                $option,
            ]);
            $this->validate($request, $validate, $this->validationMessages);

            $data = $this->model->singleUpdate($id, $request->only($option));
            logs('blogs', 'sửa trạng thái của chương trình khuyến mãi có tên ' . $data->name, $data);
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
     * Lấy ra các Trạng thái bài viết (theo status)
     * @author sonduc <ndson1998@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function statusList()
    {
        try {
            $this->authorize('promotion.view');
            $data = $this->simpleArrayToObject(Promotion::PROMOTION_STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
