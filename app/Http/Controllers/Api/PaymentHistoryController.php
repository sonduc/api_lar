<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\PaymentHistoryTransformer;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Payments\PaymentHistoryRepository;
use DB;
use Illuminate\Http\Request;

class PaymentHistoryController extends ApiController
{
    protected $validationRules
        = [
            'booking_id'     => 'required|integer|exists:bookings,id',
            'money_received' => 'required|integer',
            'confirm'        => 'required|integer|between:0,1',
        ];
    protected $validationMessages
        = [

            'booking_id.required'     => 'Vui lòng chọn booking',
            'booking_id.integer'      => 'Mã booking phải là số',
            'booking_id.exists'       => 'Booking không tồn tại',
            'money_received.required' => 'Vui lòng nhập số tiền',
            'money_received.integer'  => 'Số tiền nhập vào phải là kiểu số',
            'confirm.required'        => 'Vui lòng chọn trạng thái xác nhận',
            'confirm.integer'         => 'Trạng thái xác nhận phải là kiểu số',
            'confirm.between'         => 'Trạng thái xác nhận không phù hợp',
        ];

    /**
     * PaymentHistoryController constructor.
     *
     * @param PaymentHistoryRepository $payment
     */
    public function __construct(PaymentHistoryRepository $payment, BookingRepository $booking)
    {
        $this->model   = $payment;
        $this->booking = $booking;
        $this->setTransformer(new PaymentHistoryTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('booking.view');
        $pageSize    = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
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
            $this->authorize('booking.view');
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $booking = $this->booking->getById($request->booking_id);
            $data    = $this->model->storePaymentHistory($booking, $request->all());
            DB::commit();
            logs('payment_history', 'đã thêm thanh toán cho booking mã ' . $booking->code, $data);
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function paymentHistoryStatus()
    {
        return response()->json(BookingConstant::PAYMENT_HISTORY_STATUS);
    }
}
