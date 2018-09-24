<?php

namespace App\Http\Transformers;

use App\Repositories\Payments\PaymentHistory;
use League\Fractal\TransformerAbstract;

class PaymentHistoryTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    public function transform(PaymentHistory $payment = null)
    {
        if (is_null($payment)) {
            return [];
        }

        return [
            'id'             => $payment->id,
            'money_received' => $payment->money_received,
            'total_received' => $payment->total_received,
            'total_debt'     => $payment->total_debt,
            'note'           => $payment->note,
            'confirm'        => $payment->confirm,
            'confirm_txt'    => $payment->confirm == 1 ? 'Đã xác nhận' : 'Chưa xác nhận',
            'status'         => $payment->status,
            'status_txt'     => $payment->paymentStatus(),
            'created_at'     => $payment->created_at->format('Y-m-d H:i:s'),
            'updated_at'     => $payment->updated_at->format('Y-m-d H:i:s'),

        ];
    }

}
