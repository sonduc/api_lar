<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Coupons\Coupon;
use App\Repositories\Coupons\CouponLogic;
use Illuminate\Http\Request;

use App\Http\Transformers\CouponTransformer;
use Illuminate\Support\Facades\DB;
use App\Repositories\Coupons\CouponRepository;

class CouponController extends ApiController
{
    protected $validationRules = [
        'code'                          =>  'required|min:4|unique:coupons,code',
        'discount'                      =>  'required|integer|between:0,100',
        'max_discount'                  =>  'integer|min:0',
        'usable'                        =>  'integer|min:0',
        'used'                          =>  'integer|min:0',
        'status'                        =>  'required|integer|between:0,1',
        'settings'                      =>  'min:1',
        'settings.rooms.*'              =>  'distinct|exists:rooms,id,deleted_at,NULL',
        'settings.cities.*'             =>  'distinct|exists:cities,id,deleted_at,NULL',
        'settings.districts.*'          =>  'distinct|exists:districts,id,deleted_at,NULL',
        'settings.days.*'               =>  'distinct|date|after:now',
        'promotion_id'                  =>  'required|integer|exists:promotions,id,deleted_at,NULL',
    ];
    protected $validationMessages = [
        'code.required'                 =>  'Mã giảm giá không được để trống',
        'code.min'                      =>  'Độ dài phải là :min',
        'code.unique'                   =>  'Mã giảm giá này đã tồn tại',
        'discount.required'             =>  'Phần trăm giảm giá không được để trống',
        'discount.integer'              =>  'Phần trăm giảm giá không phải là dạng số',
        'discount.between'              =>  'Phần trăm giảm giá không phù hợp',
        'max_discount.integer'          =>  'Số tiền tối đa được giảm không phải là số',
        'max_discount.min'              =>  'Số tiền tối đa được giảm không được dưới 0',
        'usable.integer'                =>  'Số lần sử dụng tối đa không phải là số',
        'usable.min'                    =>  'Số lần sử dụng tối đa không được dưới 0',
        'used.integer'                  =>  'Số lần đã sử dụng không phải là số',
        'used.min'                      =>  'Số lần đã sử dụng không được dưới 0',
        'status.required'               =>  'Trạng thái không được để trống',
        'status.integer'                =>  'Trạng thái không phải là dạng số',
        'status.between'                =>  'Trạng thái không phù hợp',
        'settings.min'                  =>  'Phải có ít nhất 1 điều kiện giảm giá',
        'settings.rooms.*.distinct'     =>  'Các phòng không được trùng nhau',
        'settings.rooms.*.exists'       =>  'Phòng không tồn tại',
        'settings.cities.*.distinct'    =>  'Các thành phố không được trùng nhau',
        'settings.cities.*.exists'      =>  'Thành phố không tồn tại',
        'settings.districts.*.distinct' =>  'Các quận huyện không được trùng nhau',
        'settings.districts.*.exists'   =>  'Quận huyện không tồn tại',
        'settings.days.*.distinct'      =>  'Các ngày áp dụng giảm giá không được trùng nhau',
        'settings.days.*.date'          =>  'Các ngày áp dụng giảm giá không hợp lệ',
        'settings.days.*.after'         =>  'Thời gian áp dụng cho những ngày giảm giá không được phép ở thời điểm quá khứ',
        'promotion_id.required'         =>  'Vui lòng chọn chương trình giảm giá',
        'promotion_id.integer'          =>  'Mã chương trình giảm giá phải là kiểu số',
        'promotion_id.exists'           =>  'Chương trình giảm giá không tồn tại',
    ];

    /**
     * CouponController constructor.
     * @param CouponRepository $coupon
     */
    public function __construct(CouponLogic $coupon)
    {
        $this->model = $coupon;
        $this->setTransformer(new CouponTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        $this->authorize('coupon.view');
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $this->model->getValueSetting($request->all());
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
            $this->authorize('coupon.view');
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
            $this->authorize('coupon.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
            // DB::commit();
            return $this->successResponse($data,false);
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
            $this->authorize('coupon.update');

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
            $this->authorize('coupon.delete');
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
