<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\TransformerAbstract;
use App\Repositories\Bookings\Booking;

class BookingTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Booking $booking = null)
    {
        if (is_null($booking)) {
            return [];
        }

        return [
            'id'                    => $booking->id,
            'uuid'                  => $booking->uuid,
            'code'                  => $booking->code,
            'name'                  => $booking->name,
            'sex'                   => $booking->sex,
            'sex_txt'               => $booking->getSex(),
            'birthday'              => $booking->birthday,
            'phone'                 => $booking->phone,
            'email'                 => $booking->email,
            'email_received'        => $booking->email_received,
            'name_received'         => $booking->name_received,
            'phone_received'        => $booking->phone_received,
            'room_id'               => $booking->room_id,
            'customer_id'           => $booking->customer_id,
            'merchant_id'           => $booking->merchant_id,
            'checkin'               => $booking->checkin ? date('Y-m-d H:i:s', $booking->checkin) : 'Không xác định',
            'checkout'              => $booking->checkout ? date('Y-m-d H:i:s', $booking->checkout) : 'Không xác định',
            'number_of_guests'      => $booking->number_of_guest ?? 0,
            'price_original'        => $booking->price_original,
            'price_discount'        => $booking->price_discount,
            'booking_fee'           => $booking->booking_fee,
            'coupon'                => $booking->coupon,
            'coupon_txt'            => $booking->coupon ?? 'Không áp dụng coupon',
            'note'                  => $booking->note,
            'service_fee'           => $booking->service_fee,
            'total_fee'             => $booking->total_fee,
            'booking_type'          => $booking->booking_type,
            'booking_type_txt'      => $booking->getBookingType(),
            'type'                  => $booking->type,
            'type_txt'              => $booking->getType(),
            'source'                => $booking->source,
            'source_txt'            => $booking->getBookingSource(),
            'payment_status'        => $booking->payment_status,
            'payment_status_txt'    => $booking->getPaymentStatus(),
            'status'                => $booking->status,
            'status_txt'            => $booking->getBookingStatus(),
            'price_range'           => $booking->price_range,
            'price_range_txt'       => $booking->getPriceRange(),
            'exchange_rate'         => $booking->exchange_rate,
            'created_at'            => $booking->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $booking->updated_at->format('Y-m-d H:i:s'),
        ];
    }

}
