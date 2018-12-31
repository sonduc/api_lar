<?php

namespace App\Listeners\Transactions;

use App\Events\CreateBookingTransactionEvent;
use App\Repositories\Transactions\TransactionLogic;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantReferralTransactionListener implements ShouldQueue
{
    protected $transaction;
    /**
     * Create the event listener.
     *
     * @return void
     */

    /**
     * MerchantReferralTransactionListener constructor.
     *
     * @param TransactionLogic $transaction
     */
    public function __construct(TransactionLogic $transaction)
    {
        $this->transaction = $transaction;
    }


    public function handle(CreateBookingTransactionEvent $event)
    {
        $this->transaction->createReferralTransaction($event);
    }
}
