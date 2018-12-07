<?php

namespace App\Http\Transformers;

use App\Helpers\ErrorCore;
use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Bookings\Booking;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class BookingTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'customer',
        'merchant',
        'booking_status',
        'payments',
        'room',
        'city',
        'district',
        'cancel',
        'reviews'
    ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking|null $booking
     *
     * @return array
     */
    public function transform(Booking $booking = null)
    {
        if (is_null($booking)) {
            return [];
        }

        return [
            'id'                 => $booking->id,
            'uuid'               => $booking->uuid,
            'code'               => $booking->code,
            'name'               => $booking->name,
            'sex'                => $booking->sex,
            'sex_txt'            => $booking->getSex(),
            'birthday'           => $booking->birthday,
            'phone'              => $booking->phone,
            'email'              => $booking->email,
            'email_received'     => $booking->email_received,
            'name_received'      => $booking->name_received,
            'phone_received'     => $booking->phone_received,
            'room_id'            => $booking->room_id,
            'customer_id'        => $booking->customer_id,
            'merchant_id'        => $booking->merchant_id,
            'checkin'            => $booking->checkin ? date('Y-m-d H:i:s', $booking->checkin) : trans2(ErrorCore::UNDEFINED),
            'checkout'           => $booking->checkout ? date('Y-m-d H:i:s', $booking->checkout) : trans2(ErrorCore::UNDEFINED),
            'number_of_guests'   => $booking->number_of_guests ?? 0,
            'price_original'     => $booking->price_original,
            'price_discount'     => $booking->price_discount,
            'coupon_discount'    => $booking->coupon_discount,
            'coupon'             => $booking->coupon,
            'coupon_txt'         => $booking->coupon ?? 'Không áp dụng coupon',
            'note'               => $booking->note,
            'service_fee'        => $booking->service_fee,
            'additional_fee'     => $booking->additional_fee,
            'total_fee'          => $booking->total_fee,
            'booking_type'       => $booking->booking_type,
            'booking_type_txt'   => $booking->getBookingType(),
            'type'               => $booking->type,
            'type_txt'           => $booking->getType(),
            'source'             => $booking->source,
            'source_txt'         => $booking->getBookingSource(),
            'payment_status'     => $booking->payment_status,
            'payment_status_txt' => $booking->getPaymentStatus(),
            'payment_method'     => $booking->payment_method,
            'payment_method_txt' => $booking->getPaymentMethod(),
            'status'             => $booking->status,
            'status_txt'         => $booking->getBookingStatus(),
            'email_reminder'     => $booking->email_reminder,
            'email_reminder_txt' => $booking->getEmailReminder(),
            'email_reviews'      => $booking->email_reviews,
            'email_reviews_txt'  => $booking->getEmailReviews(),
            'price_range'        => $booking->price_range,
            'price_range_txt'    => $booking->getPriceRange(),
            'exchange_rate'      => $booking->exchange_rate,
            'total_refund'       =>  $booking->total_refund,
            'total_txt'          =>  $booking->getTotalRefund($booking),
            'settings'           =>  json_decode($booking->settings),
            'created_at'         => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'         => $booking->updated_at ? $booking->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeCustomer(Booking $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        return $this->item($booking->customer, new UserTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeMerchant(Booking $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        return $this->item($booking->merchant, new UserTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeBookingStatus(Booking $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        return $this->item($booking->bookingStatus, new BookingStatusTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking|null  $booking
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includePayments(Booking $booking = null, ParamBag $params = null)
    {
        if (is_null($booking)) {
            return $this->null();
        }
        $data = $this->pagination($params, $booking->payments());
        return $this->collection($data, new PaymentHistoryTransformer);
    }

    public function includeCancel(Booking $booking = null, ParamBag $params = null)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        $data = $this->pagination($params, $booking->cancel());

        return $this->collection($data, new BookingCancelTransformer());
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     */
    public function includeRoom(Booking $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        try {
            if (is_null($booking->room)) throw new \Exception;
            return $this->item($booking->room, new RoomTransformer);
        } catch (\Exception $e) {
            return $this->primitive(null);
        }
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     */
    public function includeCity(Booking $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        try {
            if (is_null($booking->room->city)) throw new \Exception;
            return $this->item($booking->room->city, new CityTransformer);
        } catch (\Exception $e) {
            return $this->primitive(null);
        }
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking $booking
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     */
    public function includeDistrict(Booking $booking)
    {
        if (is_null($booking)) {
            return $this->null();
        }

        try {
            if (is_null($booking->room->district)) throw new \Exception;
            return $this->item($booking->room->district, new DistrictTransformer);
        } catch (\Exception $e) {
            return $this->primitive(null);
        }
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Booking|null $booking
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */

    public function includeReviews(Booking $booking = null, ParamBag $params = null)
    {

        if (is_null($booking)) {
            return $this->null();
        }

        return $this->item($booking->reviews, new RoomReviewTransformer);
    }
}
