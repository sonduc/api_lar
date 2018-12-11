<?php

namespace App\Repositories\Payments;

use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;


class PaymentHistoryRepository extends BaseRepository implements PaymentHistoryRepositoryInterface
{
    /**
     * PaymentHistory model.
     * @var Model
     */
    protected $model;

    /**
     * PaymentHistoryRepository constructor.
     *
     * @param PaymentHistory $payment
     */
    public function __construct(PaymentHistory $payment)
    {
        $this->model = $payment;
    }

    /**
     * Thêm vào lịch sử thanh toán
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function storePaymentHistory($booking = [], $data = [])
    {
        $data['booking_id']     = $booking->id;
        $data['total_received'] = $this->totalMoneyPaid($booking);
        $data                   = $this->processPaymentMoney($booking, $data);
        $data                   = $this->processPaymentStatus($booking, $data);
        $data                   = $this->processPaymentNote($booking, $data);
        return parent::store($data);
    }

    /**
     * Tổng số tiền đã thanh toán của booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $booking
     *
     * @return mixed
     */
    public function totalMoneyPaid($booking)
    {
        return $this->model->where('booking_id', $booking->id)->sum('money_received');
    }

    /**
     * Hàm xử lý tiền
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     * @param array $data
     *
     * @return array
     */
    public function processPaymentMoney($booking = [], $data = [])
    {
        $debt = $booking->total_fee - $data['total_received'];
        if (array_key_exists('money_received', $data) && is_numeric($data['money_received'])) {
            $debt                   -= $data['money_received'];
            $data['total_received'] += $data['money_received'];
        }

        $data['total_debt'] = $debt;
        return $data;
    }

    /**
     * Hàm xử lý trạng thái thanh toán
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     * @param array $data
     *
     * @return array
     */
    public function processPaymentStatus($booking = [], $data = [])
    {
        $data['status'] = ($data['total_received'] == 0)
            ? BookingConstant::UNPAID
            : ($data['total_received'] >= $booking->total_fee ? BookingConstant::FULLY_PAID
                : BookingConstant::PARTLY_PAID);

        return $data;
    }

    /**
     * Viết ghi chú cho thanh toán
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     * @param array $data
     *
     * @return array
     */
    public function processPaymentNote($booking = [], $data = [])
    {
        $money_received =
            (array_key_exists('money_received', $data) ? number_format($data['money_received']) : 0) . 'đ';
        $total_received =
            (array_key_exists('total_received', $data) ? number_format($data['total_received']) : 0) . 'đ';

        $debt = (array_key_exists('total_debt', $data) ? number_format($data['total_debt']) : 0) . 'đ';
        $code = $booking->code;

        switch ($data['status']) {
            case BookingConstant::UNPAID:
                $data['note'] = 'Chưa thanh toán';
                break;
            case BookingConstant::PARTLY_PAID:
                $data['note'] = 'Khách thanh toán ' . $money_received . ' cho booking mã: ' . $code . '. Tổng đã nhận: '
                                . $total_received . '. Còn thiếu: ' . $debt;
                break;
            case BookingConstant::FULLY_PAID:
                $data['note'] =
                    'Khách thanh toán thành công cho booking mã: ' . $code . '. Tổng đã nhận: ' . $total_received;
                break;
            default:
                $data['note'] = 'Chưa thanh toán';
                break;
        }

        return $data;
    }

    /**
     * Xóa payment_histories theo mã booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     */
    public function destroyPaymentHistoriesByBookingId($booking = [])
    {
        $this->model->where('booking_id', $booking->id)->forceDelete();
    }
}
