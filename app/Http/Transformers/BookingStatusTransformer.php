<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Bookings\BookingStatus;

class BookingStatusTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user'
    ];

    public function transform(BookingStatus $booking = null)
    {
        if (is_null($booking)) {
            return [];
        }

        return [
            'id'                    => $booking->id,
            'staff_id'              => $booking->staff_id,
            'booking_id'            => $booking->booking_id,
            'note'                  => $booking->note,
            'created_at'            => $booking->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $booking->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function includeUser(BookingStatus $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        return $this->item($booking->user, new UserTransformer);
    }
}
