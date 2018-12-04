<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 09:20
 */

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Transformers\Customer\WishListTransformer;
use App\Repositories\_Customer\WishListLogic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishListController extends ApiController
{
    protected $room;
    protected $validationRules = [

        'room_id'               => 'required|integer|unique:wish_lists,room_id|exists:rooms,id,deleted_at,NULL',

    ];
    protected $validationMessages = [
        'room_id.required'      => 'Trường này không được để trống',
        'room_id.integer'       => 'Trường này phải là kiểu số',
        'room_id.unique'        => 'Phòng này đã tồn tại trong danh sách',
        'room_id.exists'        => 'Phòng không tồn tại',
    ];

    /**
     * WishlistController constructor.
     * @param WishlistRepository $wishlist
     */
    public function __construct(WishListLogic $wish_list)
    {
        $this->model = $wish_list;
        $this->setTransformer(new WishListTransformer);
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
        DB::enableQueryLog();
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data = $this->model->getByQuery($request->all(), $pageSize);
       //  dd(DB::getQueryLog());
        return $this->successResponse($data);
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
            $data = $this->model->getById($id);
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
     * Store a record into database
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return mixed
     * @throws Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());
            DB::commit();
            return $this->successResponse($data, true, 'rooms');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Update a record
     *
     * @param Request $request
     * @param         $id
     *
     * @return mixed
     * @throws Throwable
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $model = $this->model->update($id, $request->all());
            DB::commit();
            return $this->successResponse($model, true, 'rooms');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors' => $validationException->validator->errors(),
                'exception' => $validationException->getMessage()
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
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
            $this->model->destroy($id);

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
