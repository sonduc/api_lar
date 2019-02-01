<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 2/1/2019
 * Time: 12:06 AM
 */

namespace App\Http\Controllers\ApiCustomer;


use App\Repositories\_Customer\RoomReviewLogic;
use App\Repositories\Rooms\RoomReview;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\AverageRoomRating;

class RoomReviewController extends ApiController
{
    protected $validationRules    = [
        'booking_id'     => 'required|integer|exists:bookings,id,deleted_at,NULL',
        'cleanliness'    => 'nullable|integer|between:1,5',
        'service'        => 'nullable|integer|between:1,5',
        'quality'        => 'nullable|integer|between:1,5',
        'valuable'       => 'nullable|integer|between:1,5',
        'avg_rating'     => 'nullable|numeric|between:1,5',
        'recommend'      => 'nullable|integer|between:0,1',
        'like'           => 'nullable|integer|between:0,1',
    ];
    protected $validationMessages = [
        'booking_id.required'     => 'Vui lòng chọn mã booking',
        'booking_id.integer'     => 'Mã booking phải là kiểu số',
        'booking_id.exists'      => 'Booking không tồn tại',

        'cleanliness.integer'    => 'Mã đánh giá sạch sẽ phải là kiểu số',
        'cleanliness.between'    => 'Mã đánh giá sạch sẽ không phù hợp',
        'service.integer'        => 'Mã đánh giá dịch vụ phải là kiểu số',
        'service.between'        => 'Mã đánh giá dịch vụ không phù hợp',
        'quality.integer'        => 'Mã đánh giá chất lượng phòng phải là kiểu số ',
        'quality.between'        => 'Mã đánh giá chất lượng không phù hợp',
        'valuable.integer'       => 'Mã đánh giá độ xứng đáng phải là kiểu số',
        'valuable.between'       => 'Mã  đánh giá đọ xứng đáng không phù h',
        'avg_rating.numeric'     => 'Mã  đánh giá tổng hợp phải là kiểu số',
        'avg_rating.between'     => 'Mã  đánh giá tổng hợp không phù hợp',
        'recommend.integer'      => 'Mã giới thiệu phải là kiểu số',
        'recommend.between'      => 'Mã giới thiệu không phù hợp',
        'like.integer'           => 'Mã thích phải là kiểu số',
        'like.between'           => 'Mã thích phải là kiểu số',
    ];

    /**
     * RoomReviewController constructor.
     *
     * @param RoomReviewLogic $review
     */
    public function __construct(RoomReviewLogic $review)
    {
        $this->model = $review;
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
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());
            event(new AverageRoomRating($data->room_id, $data));
            // dd($data->room_id);
            DB::commit();
            logs('room-review', 'thêm mới review từ host cho hệ thống' . $data->id, $data);
            return $this->successResponse(['message' => 'Hoàn thành Review'],false);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
    /**
     * Type Like
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewLikeList()
    {
        try {
            $data = $this->simpleArrayToObject(RoomReview::LIKE);
            return response()->json($data);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Type giới thiêuj
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewRecommendList()
    {
        try {
            $data = $this->simpleArrayToObject(RoomReview::RECOMMEND);
            return response()->json($data);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Type Service
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewServiceList()
    {
        try {
            $data = $this->simpleArrayToObject(RoomReview::SERVICE);
            return response()->json($data);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Type Quality
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewQualityList()
    {
        try {
            $data = $this->simpleArrayToObject(RoomReview::QUALITY);
            return response()->json($data);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * Type CleanlinessList
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewCleanlinessList()
    {
        try {
            $data = $this->simpleArrayToObject(RoomReview::CLEANLINESS);
            return response()->json($data);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Type ValueableList
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewValuableList()
    {
        try {
            $data = $this->simpleArrayToObject(RoomReview::VALUABLE);
            return response()->json($data);
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}