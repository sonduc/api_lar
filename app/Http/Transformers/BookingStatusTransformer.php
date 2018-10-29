<?php

namespace App\Http\Transformers;

use App\Repositories\Bookings\BookingStatus;
use League\Fractal\TransformerAbstract;

class BookingStatusTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user',
    ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param BookingStatus|null $booking
     *
     * @return array
     */
    public function transform(BookingStatus $booking = null)
    {
        if (is_null($booking)) {
            return [];
        }

        return [
            'id'         => $booking->id,
            'staff_id'   => $booking->staff_id,
            'booking_id' => $booking->booking_id,
            'note'       => $booking->note,
            'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $booking->updated_at ? $booking->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param BookingStatus|null $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeUser(BookingStatus $booking = null)
    {
        if (is_null($booking) || is_null($booking->user)) {
            return $this->null();
        }

        return $this->item($booking->user, new UserTransformer);
    }
}
