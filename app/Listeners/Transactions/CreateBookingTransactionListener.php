<?php

namespace App\Listeners\Transactions;

use App\Events\CreateBookingTransactionEvent;
use App\Repositories\Transactions\TransactionLogic;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateBookingTransactionListener implements ShouldQueue
{
    protected $transaction;
    /**
     * Create the event listener.
     *
     * @return void
     */

    /**
     * CreateBookingTransactionListener constructor.
     *
     * @param TransactionLogic $transaction
     */
    public function __construct(TransactionLogic $transaction)
    {
        $this->transaction = $transaction;
    }


    public function handle(CreateBookingTransactionEvent $event)
    {
        $this->transaction->createBookingTransaction($event);
    }
}
