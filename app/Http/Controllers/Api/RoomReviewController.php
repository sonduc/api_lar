<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Rooms\RoomReview;
use App\Repositories\Rooms\RoomReviewLogic;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Transformers\RoomReviewTransformer;
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
        'status_reviews' => 'nullable|integer|between:0,1',
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
        'status_reviews.integer' => 'Mã trạng thái đánh gía phải là kiểu số',
        'status_reviews.between' => 'Mã nổi trạng thái đánh gía không phù hợp',
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
        $this->setTransformer(new RoomReviewTransformer);
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
        try {
            $this->authorize('room.view');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            return $this->successResponse($data);
        }catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
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
            $this->authorize('room.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        }catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
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
            $this->authorize('room.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());
            event(new AverageRoomRating($data->room_id, $data));
            // dd($data->room_id);
            DB::commit();
            return $this->successResponse($data);
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
     * Cập nhaật Room_Review
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('room.update');
            $this->validationRules['booking_id'] = '';

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $model = $this->model->update($id, $request->all());
            DB::commit();
            return $this->successResponse($model);
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
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
            $this->authorize('room.delete');
            $this->model->delete($id);

            return $this->deleteResponse();
        }catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
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
     * Type review;
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function reviewStatusList()
    {
        try {
            $this->authorize('room.view');
            $data = $this->simpleArrayToObject(RoomReview::ROOM_REVIEW_STATUS);
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
     * Type Like
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reviewLikeList()
    {
        try {
            $this->authorize('room.view');
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
            $this->authorize('room.view');
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
            $this->authorize('room.view');
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
            $this->authorize('room.view');
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
            $this->authorize('room.view');
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
            $this->authorize('room.view');
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
