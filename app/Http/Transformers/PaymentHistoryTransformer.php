<?php

namespace App\Http\Transformers;

use App\Repositories\Payments\PaymentHistory;
use League\Fractal\TransformerAbstract;

class PaymentHistoryTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param PaymentHistory|null $payment
     *
     * @return array
     */
    public function transform(PaymentHistory $payment = null)
    {
        if (is_null($payment)) {
            return [];
        }

        return [
            'id'             => $payment->id,
            'booking_id'     => $payment->booking_id,
            'money_received' => $payment->money_received,
            'total_received' => $payment->total_received,
            'total_debt'     => $payment->total_debt,
            'note'           => $payment->note,
            'confirm'        => $payment->confirm,
            'confirm_txt'    => $payment->confirm == 1 ? 'Đã xác nhận' : 'Chưa xác nhận',
            'status'         => $payment->status,
            'status_txt'     => $payment->paymentStatus(),
            'created_at'     => $payment->created_at,
            'updated_at'     => $payment->updated_at,

        ];
    }

}
