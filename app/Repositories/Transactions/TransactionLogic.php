<?php

namespace App\Repositories\Transactions;

use App\User;
use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Referrals\ReferralRepositoryInterface;
use App\Repositories\TransactionTypes\TransactionType;
use App\Repositories\Bookings\BookingConstant;

class TransactionLogic extends BaseLogic
{
    use TransactionLogicTrait;


    public function __construct(
        TransactionRepositoryInterface $transaction,
        BookingRepositoryInterface $booking,
        UserRepositoryInterface $user,
        RoomRepositoryInterface $room,
        ReferralRepositoryInterface $ref
    ) {
        $this->model    = $transaction;
        $this->booking  = $booking;
        $this->user     = $user;
        $this->room     = $room;
        $this->ref      = $ref;
    }

    public function createBookingTransaction($dataBooking)
    {
        $dataBooking = $dataBooking->data;

        $room_id     = $dataBooking['room_id'];
        $merchant_id = $dataBooking['merchant_id'];
        $date        = Carbon::parse($dataBooking['created_at'])->toDateString();
        $booking_id  = $dataBooking['id'];
        $commission  = $this->room->getRoomCommission($room_id);
        $type        = TransactionType::TRANSACTION_BOOKING;
        $credit      = $dataBooking['total_fee'];
        $bonus       = 0;

        $credit = ($dataBooking['status'] == BookingConstant::BOOKING_CANCEL) ? ($dataBooking['total_fee'] - $dataBooking['total_refund']) : 0;

        $debit = ($dataBooking['status'] == BookingConstant::BOOKING_NEW || $dataBooking['status'] == BookingConstant::BOOKING_CONFIRM) ? $dataBooking['total_fee'] : 0;

        $dataTransaction = [
            'type'          => $type,
            'date_create'   => $date,
            'user_id'       => $merchant_id,
            'room_id'       => $room_id,
            'booking_id'    => $booking_id,
            'credit'        => (int) ceil($credit),
            'debit'         => (int) ceil($debit),
            'bonus'         => $bonus,
            'commission'    => $commission
        ];
        
        return parent::store($dataTransaction);
    }

    public function createBonusTransaction()
    {
        $end_checkout                = $now->startOfDay()->timestamp;
        $start_checkout              = $now->subDay()->timestamp;
        $total_fee                   = 1000000;
        $date                        = Carbon::now()->toDateString();
        $referral_merchant_list      = $this->ref->getAllReferralUser(null, null, User::MERCHANT);
        $list_merchant_first_booking = $this->booking->getMerchantFirstBooking($list_refer_id, $start_checkout, $end_checkout, $total_fee);

        if (count($list_merchant_first_booking)) {
            $listMerchant = $this->ref->getAllReferralUser(null, $list_merchant_first_booking, User::MERCHANT);
        } else {
            return null;
        }

        foreach ($listMerchant as $key => $value) {
            $dataTransaction = [
                'type'          => TransactionType::TRANSACTION_BONUS,
                'date_create'   => $date,
                'user_id'       => $value,
                'credit'        => 0,
                'debit'         => 0,
                'bonus'         => 150000,
                'commission'    => 0
            ];

            $this->referral->updateStatusReferral($value['user_id'], $value['refer_id'], User::USER);
            
            parent::store($dataTransaction);
        }
    }
}
