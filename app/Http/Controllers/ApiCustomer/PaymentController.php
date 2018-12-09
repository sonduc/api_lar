<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 08/12/2018
 * Time: 09:31
 */

namespace App\Http\Controllers\ApiCustomer;


use Illuminate\Http\Request;
//use Nht\Hocs\BaoKim\BaoKimTradeHistoryRepository;
//use Nht\Events\UpdateHistory;
//use Nht\Hocs\Booking\BookingRepositoryEloquent;
//use Nht\Hocs\Helpers\BookingConstant;
//use Nht\Hocs\Helpers\BookingProcessor;
//use Illuminate\Support\Facades\DB;
//use Nht\Jobs\SendBookingEmail;
//use Nht\Jobs\SendBookingHostEmail;
//use Nht\Jobs\SendBookingCustomerEmail;

class PaymentController extends ApiController
{
//    public function __construct(
//        BaoKimTradeHistoryRepository  $baokim,
//        BookingRepositoryEloquent     $booking,
//        BookingProcessor              $bookingProcessor
//    )
//    {
//        parent::__construct();
//        $this->booking             = $booking;
//        $this->baokim              = $baokim;
//        $this->bookingProcessor    = $bookingProcessor;
//    }

    /**
     * Booking success
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function success(Request $request)
    {
        if (is_null($request['transaction_id'])) {
            return $this->fail($request['order_id']);
        }
        // Lấy thông tin booking theo mã code nhận được từ bảo kim trả về
        $booking = $this->booking->getByCode($request['order_id']);
        $payment_history = [
            'money_received' => $request['total_amount'],
            'payment_type'   => BookingConstant::PAY_IN,
            'note'           => $booking->payment_method == 3 ? 'Xác nhận đã thanh toán booking mã: '. $request['order_id'] . 'từ Bảo Kim' : 'Thanh toán booking: ' . $request['order_id'] . 'qua ATM nội địa',
            'confirm'        => BookingConstant::CONFIRM,
        ];
        $request['client_id'] = 0;
        DB::beginTransaction();
        try {
            $payment = $this->baokim->store($request->all()) ;
            $time    = $this->bookingProcessor->calculatorNumberDayVsHours($booking['number_time'], $booking->type);
            DB::commit();
            if ($payment && $request['transaction_status'] == 4 || $request['transaction_status'] == 13) {
                // Cập nhật trạng thái thanh toán bảng booking
                // $booking->
                activity('booking')->withProperties(['object' => $payment->toArray(), 'ip' => \Request::ip()])->log('Thanh toan từ bảo kim cho mã: <mark>' . $booking->code . '</mark>' . ' với phòng <mark>' . ($booking->room ? $booking->room->name : '') . '</mark>');
                event(new UpdateHistory($booking, $payment_history));

                // send email cho admin
                $job = (new SendBookingEmail($booking))->onQueue('email');
                dispatch($job);

                // send email cho chủ host
                $sendHost = (new SendBookingHostEmail($booking))->onQueue('email');
                dispatch($sendHost);

                // send email cho customer
                $sendHost = (new SendBookingCustomerEmail($booking))->onQueue('email');
                dispatch($sendHost);

                return view('web.thankyou', compact('booking', 'time'));
            } else {
                if (! $payment || $request['transaction_status'] == 5 || $request['transaction_status'] == 7 || $request['transaction_status'] == 8 || $request['transaction_status'] == 12) {
                    return $this->fail($request['order_id']);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $exception) {
            DB::rollback();
            return $exception->getMessage();
        }
    }

    // Faild
    public function fail($code)
    {
        $booking = $this->booking->getByCode($code);
        if ($booking) {
            $this->booking->delete($booking->id);
        }

        return view('web.bookingfail');
    }
}

