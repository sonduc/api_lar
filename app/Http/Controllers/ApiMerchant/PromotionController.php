<?php

namespace App\Http\Controllers\ApiMerchant;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Http\Transformers\Merchant\PromotionTransformer;
use App\Repositories\Promotions\PromotionRepository;
use App\Repositories\Promotions\PromotionRepositoryInterface;
use App\Repositories\_Merchant\PromotionLogic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromotionController extends ApiController
{
    protected $validationRules = [
        'promotion_id'       => 'integer|exists:promotions,id,deleted_at,NULL',
        'merchants_id'       => 'exists:users,id,deleted_at,NULL',
        'coupon'             => 'string|without_spaces|min:4|exists:coupons,code,deleted_at,NULL',
        'rooms.*'            => 'distinct|exists:rooms,id,deleted_at,NULL',
    ];
    protected $validationMessages = [
        'promotion_id.integer'      => 'Mã chương trình giảm giá phải là kiểu số',
        'promotion_id.exists'       => 'Chương trình giảm giá không tồn tại',
        'merchants_id.exists'       => 'Mã chủ nhà không tồn tại',
        'coupon.string'             => 'Mã giảm giá không được chứa ký tự đặc biệt',
        'coupon.without_spaces'     => 'Mã giảm giá không được có khoảng trống',
        'coupon.min'                => 'Độ dài phải là :min',
        'coupon.exists'             => 'Mã giảm giá không tồn tại',
        'rooms.*.distinct'          => 'Các phòng không được trùng nhau',
        'rooms.*.exists'            => 'Phòng không tồn tại',
    ];

    /**
     * PromotionController constructor.
     * @param PromotionRepositoryInterface $promotion
     */
    public function __construct(PromotionLogic $promotion)
    {
        $this->model = $promotion;
        $this->setTransformer(new PromotionTransformer);
    }

    /**
     * Display list resource
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('promotion.view');
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
        return $this->successResponse($data);
    }

    /**
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function joinPromotion(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->authorize('promotion.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->joinPromotion($request->all());
            DB::commit();
            return $this->successResponse($data, false);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        } 
    }

}
