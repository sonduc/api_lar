<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/22/2019
 * Time: 9:59 AM
 */

namespace App\Http\Controllers\ApiMerchant;


use App\Http\Controllers\Api\ApiController;
use App\Http\Transformers\Merchant\BookingTransformer;
use App\Repositories\_Merchant\BookingLogic;
use App\Repositories\Bookings\BookingConstant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends ApiController
{

    protected $validationRules    = [
        'additional_fee'            => 'filled|integer|min:0',
        'price_discount'            => 'filled|integer|min:0',

        'status'                    => 'integer|between:2,4',
    ];
    protected $validationMessages = [
        'additional_fee.required'   => 'Vui lòng điền giá',
        'additional_fee.filled'     => 'Vui lòng điền giá',
        'additional_fee.integer'    => 'Giá phải là kiểu số',
        'price_discount.required'   => 'Vui lòng điền giá',
        'price_discount.filled'     => 'Vui lòng điền giá',
        'price_discount.integer'    => 'Giá phải là kiểu số',

        'status.integer'            => 'Mã trạng thái phải là kiểu số',
        'status.between'            => 'Mã trạng thái không phù hợp',
    ];


    /**
     * BookingController constructor.
     *
     * @param BookingLogic            $booking
     * @param RoomRepositoryInterface|RoomRepository $room
     * @param UserRepositoryInterface|UserRepository $user
     */
    public function __construct(
        BookingLogic $booking
    ) {
        $this->model                = $booking;
        $this->setTransformer(new BookingTransformer);
    }


    /**
     * Lấy ra danh sách tất cả các booking của chủ host mà được khách hàng đặt
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        try {
           // $this->authorize('booking.view');
            $id   =  Auth::user()->id;
            $pageSize    = $request->get('size');
            $data = $this->model->getBooking($id, $request->all(),$pageSize);
            //   dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $this->model->checkOwnerBooking($id);
            $data    = $this->model->getById($id);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $t) {
            throw $t;
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
    public function updateBookingMoney(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->model->checkOwnerBooking($id);
            $validate = array_only($this->validationRules, [
                'additional_fee',
                'price_discount',
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $avaiable_option = array_keys($validate);
            $data = $this->model->updateBookingMoney($id, $request->only($avaiable_option));
            logs('booking', 'sửa tiền của booking có code ' . $data->code, $data);

            DB::commit();
            return $this->successResponse($data);
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
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }


    public function updateBookingStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->model->checkOwnerBooking($id);
            $validate = array_only($this->validationRules, [
                'status',
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->updateBookingStatus($id, $request->only('status'));
            logs('booking', 'sửa trạng thái của booking có code ' . $data->code, $data);
            DB::commit();
            return $this->successResponse($data);
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
     * Trạng thái booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bookingStatusList()
    {
        try {
            $this->authorize('booking.view');
            $booking_status = array_except(BookingConstant::BOOKING_STATUS,[BookingConstant::BOOKING_NEW,BookingConstant::BOOKING_CANCEL]);
            $data = $this->simpleArrayToObject($booking_status);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}