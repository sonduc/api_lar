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
        'status' => 'nullable|integer|between:0,1',
    ];
    protected $validationMessages = [

        'status.integer' => 'Mã trạng thái đánh gía phải là kiểu số',
        'status.between' => 'Mã nổi trạng thái đánh gía không phù hợp',

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
     * @author ducchien0612 <ducchien0612@gmail.com>
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
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
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
     * Update status của room-review
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->updateStatus($id, $request->only('status'));
            //dd(DB::getQueryLog());
            DB::commit();
            logs('room-review', 'Cập nhật room-review cho hệ thống mã' . $model->id, $model);
            //dd(DB::getQueryLog());
            return $this->successResponse($model);
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
