<?php

namespace App\Http\Transformers;

use App\Helpers\ErrorCore;
use App\Repositories\Bookings\BookingCancel;
use League\Fractal\TransformerAbstract;

class BookingCancelTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(BookingCancel $booking = null)
    {
        if (is_null($booking)) {
            return [];
        }

        return [
            'id'         => $booking->id,
            'code'       => $booking->code,
            'code_txt'   => $booking->getBookingCancelReasonList(),
            'note'       => $booking->note,
            'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : trans2(ErrorCore::UNDEFINED),
            'updated_at' => $booking->updated_at ? $booking->updated_at->format('Y-m-d H:i:s') : trans2(ErrorCore::UNDEFINED),
        ];
    }

}
