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
use App\Repositories\CompareCheckings\CompareCheckingRepositoryInterface;
use Carbon\Carbon;

class TransactionLogic extends BaseLogic
{
    use TransactionLogicTrait;

    public function __construct(
        TransactionRepositoryInterface $transaction,
        BookingRepositoryInterface $booking,
        UserRepositoryInterface $user,
        RoomRepositoryInterface $room,
        ReferralRepositoryInterface $ref,
        CompareCheckingRepositoryInterface $compare
    ) {
        $this->model    = $transaction;
        $this->booking  = $booking;
        $this->user     = $user;
        $this->room     = $room;
        $this->ref      = $ref;
        $this->compare  = $compare;
    }

    public function createBookingTransaction($dataBooking)
    {
        $dataBooking = $dataBooking->data;

        $room_id     = $dataBooking['room_id'];
        $merchant_id = $dataBooking['merchant_id'];
        $date        = Carbon::parse($dataBooking['checkin'])->toDateString();
        $booking_id  = $dataBooking['id'];
        $comission   = $this->room->getRoomComission($room_id);
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
            'comission'     => $comission
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
                'comission'     => 0
            ];

            $this->referral->updateStatusReferral($value['user_id'], $value['refer_id'], User::USER);
            
            parent::store($dataTransaction);
        }
    }

    /**
     * Thêm transaction mới
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */

    public function store($data)
    {
        $data = $this->getTransactionType($data);

        $now = Carbon::now()->toDateString();
        $data['date_create'] = $now;
        $dataTransaction = parent::store($data);
        return $dataTransaction;
    }
    
    /**
     * Thêm transaction mới
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */

    public function getTransactionType($data)
    {
        $type_credit = [
            TransactionType::TRANSACTION_PENALTY,
            TransactionType::TRANSACTION_SURCHARGE,
            TransactionType::TRANSACTION_RECEIPT
        ];
        
        $type_debit  = [
            TransactionType::TRANSACTION_BOOKING,
            TransactionType::TRANSACTION_DISCOUNT,
            TransactionType::TRANSACTION_PAYOUT,
            TransactionType::TRANSACTION_BOOK_AIRBNB,
            TransactionType::TRANSACTION_BOOK_BOOKING,
            TransactionType::TRANSACTION_BOOK_AGODA
        ];

        $type_bonus  = [
            TransactionType::TRANSACTION_BONUS
        ];
        
        if (isset($data['type'])) {
            if (in_array($data['type'], $type_credit)) {
                $data['credit'] = $data['money'];
            } elseif (in_array($data['type'], $type_debit)) {
                $data['debit'] = $data['money'];
            } else {
                $data['bonus'] = $data['money'];
            }
        }
        
        return $data;
    }
    
    /**
     * Tổng hợp transaction để đối soát
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */

    public function combineTransaction()
    {
        $date = Carbon::now()->subDay()->toDateString();
        
        $listUser = $this->model->getListUserCombine($date);

        $listComissionType = [
            TransactionType::TRANSACTION_BOOKING,
            TransactionType::TRANSACTION_SURCHARGE,
            TransactionType::TRANSACTION_DISCOUNT,
            TransactionType::TRANSACTION_PAYOUT,
            TransactionType::TRANSACTION_RECEIPT
        ];

        $listNoComissionType = [
            TransactionType::TRANSACTION_PENALTY,
            TransactionType::TRANSACTION_BONUS,
            TransactionType::TRANSACTION_BOOK_AIRBNB,
            TransactionType::TRANSACTION_BOOK_BOOKING,
            TransactionType::TRANSACTION_BOOK_AGODA,
        ];

        foreach ($listUser as $key => $value) {
            $user_transactions = $this->model->getUserTransaction($value);
            $total_debit  = 0;
            $total_credit = 0;
            $total_bonus  = 0;
            foreach ($user_transactions as $k => $v) {
                if ($v['comission'] !== null && in_array($v['type'], $listComissionType)) {
                    if ($v['type'] == TransactionType::TRANSACTION_BOOKING) {
                        $total_debit  += $v['debit']  * (100 - $v['comission']) / 100;
                    }
                    if ($v['type'] == TransactionType::TRANSACTION_SURCHARGE) {
                        $total_debit  += $v['debit']  * (100 - $v['comission']) / 100;
                    }
                    if ($v['type'] == TransactionType::TRANSACTION_DISCOUNT) {
                        $total_credit -= $v['credit'] * (100 - $v['comission']) / 100;
                    }
                    if ($v['type'] == TransactionType::TRANSACTION_PAYOUT) {
                        $total_credit -= $v['credit'] * (100 - $v['comission']) / 100;
                    }
                    if ($v['type'] == TransactionType::TRANSACTION_RECEIPT) {
                        $total_debit -= $v['debit'] * (100 - $v['comission']) / 100;
                    }
                } elseif ($v['comission'] == null && in_array($v['type'], $listNoComissionType)) {
                    $total_debit  += $v['debit'];
                    $total_credit -= $v['credit'];
                }
                $total_bonus      += $v['bonus'];
                $v['status'] = 1;
                $v->save();
            }
            
            $this->compare->storeCompareChecking($date, $total_debit, $total_credit, $total_bonus, $value);
        }
    }
}
