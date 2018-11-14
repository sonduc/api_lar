<?php

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Transformers\Customer\RoomTransformer;
use App\Repositories\_Customer\RoomLogic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends ApiController
{
    protected $validationRules    = [

    ];
    protected $validationMessages = [

    ];

    /**
     * RoomController constructor.
     *
     * @param RoomLogic $room
     */
    public function __construct(RoomLogic $room)
    {
        $this->model = $room;
        $this->setTransformer(new RoomTransformer);
    }


    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            DB::enableQueryLog();
            $data = $this->model->getRooms($request->all());
//           dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        try {
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


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function getRoomSchedule($id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $data = [
                'data' => [
                    'blocks' => $this->model->getFutureRoomSchedule($id),
                ],
            ];
            return $this->successResponse($data, false);
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
