<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 10/12/2018
 * Time: 15:04
 */

namespace App\Http\Controllers\ApiCustomer;


use App\Http\Transformers\PaymentHistoryTransformer;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Payments\PaymentHistoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\BookingEvent;

class PaymentHistoryController extends ApiController
{
    protected $booking;
    /**
     * PaymentHistoryController constructor.
     *
     * @param PaymentHistoryRepository $payment
     */
    public function __construct(PaymentHistoryRepositoryInterface $payment,BookingRepositoryInterface $booking)
    {
        $this->model   = $payment;
        $this->booking = $booking;
        $this->setTransformer(new PaymentHistoryTransformer());
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|string
     */
    public function success(Request $request)
    {
        DB::beginTransaction();
        try {
            $transaction = $request['transaction_id'];
            if (empty($transaction)) {
                return $this->cancel($request['order_id']);
            }
            if ($request['transaction_status'] == '' || $request['transaction_status'] == 13) {
                // Lấy thông tin booking theo mã code nhận được từ bảo kim trả về
                $booking = $this->booking->getBookingByCode($request['order_id']);
                $payment_history = [
                    //  'money_received' => $request['total_amount'],
                    //  'payment_type'   => BookingConstant::PAY_IN,
                    'note'           => $booking->payment_method == BookingConstant::PAID ? 'Xác nhận đã thanh toán booking mã: '. $request['order_id'] . 'từ Bảo Kim' : 'Thanh toán booking: ' . $request['order_id'] . 'qua ATM nội địa',
                    'confirm'        => BookingConstant::CONFIRM,
                    'status'         => BookingConstant::FULLY_PAID,
                ];

                $data    = $this->model->storePaymentHistory($booking, $payment_history);
                logs('payment_history', 'đã thêm thanh toán cho booking mã ' . $booking->code, $data);

                DB::commit();

                event(new BookingEvent($data));

                return response()->json(['message' => 'Cám ơn bạn đã sử dụng dich vụ của WESTAY']);
            } else {
                if ( $request['transaction_status'] == 5 || $request['transaction_status'] == 7 || $request['transaction_status'] == 8 || $request['transaction_status'] == 12) {
                    return $this->cancel($request['order_id']);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $exception) {
            DB::rollback();
            return $exception->getMessage();
        }
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($code)
    {
        $response = [
            'code'    => $code,
            'status'  => 'error',
            'message' => 'Thanh toán thất bại',
        ];

        return response()->json($response);

    }

}
