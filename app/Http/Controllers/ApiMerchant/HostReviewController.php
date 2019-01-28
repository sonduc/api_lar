<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 5:21 PM
 */

namespace App\Http\Controllers\ApiMerchant;


use App\Http\Controllers\Api\ApiController;
use App\Repositories\HostReviews\HostReviewRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Description;

class HostReviewController extends ApiController
{
    protected $validationRules    = [
        'booking_id'                                => 'required|integer|exists:bookings,id,deleted_at,NULL',
        'cleanliness'                               => 'nullable|integer|between:1,5',
        'friendly'                                  => 'nullable|integer|between:1,5',
        'avg_rating'                                => 'nullable|numeric|between:1,5',
        'recommend'                                 => 'nullable|integer|between:0,1',
        'house_rules_observe'                       => 'nullable|integer|between:0,1',

    ];
    protected $validationMessages = [
        'booking_id.required'                       => 'Vui lòng chọn mã booking',
        'booking_id.integer'                        => 'Mã booking phải là kiểu số',
        'booking_id.exists'                         => 'Booking không tồn tại',

        'cleanliness.integer'                       => 'Mã đánh giá sạch sẽ phải là kiểu số',
        'cleanliness.between'                       => 'Mã đánh giá sạch sẽ không phù hợp',

        'avg_rating.numeric'                        => 'Mã  đánh giá tổng hợp phải là kiểu số',
        'avg_rating.between'                        => 'Mã  đánh giá tổng hợp không phù hợp',

        'recommend.integer'                         => 'Mã giới thiệu phải là kiểu số',
        'recommend.between'                         => 'Mã giới thiệu không phù hợp',

        'recomhouse_rules_observemend.integer'      => 'Trường này phải là kiểu số',
        'house_rules_observe.between'               => 'Trường này không hợp lê'

    ];

    /**
     * HostReviewController constructor.
     * @param HostReviewRepositoryInterface $hostReview
     */
    public function __construct(HostReviewRepositoryInterface $hostReview)
    {
        $this->model = $hostReview;
    }

    public function getFormReview()
    {
        return response()->json('Form Review');

    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('host-review', 'thêm mới review từ host cho hệ thống' . $model->id, $model);
            return $this->successResponse(['message' => 'Hoàn thành Review'],false);
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

}