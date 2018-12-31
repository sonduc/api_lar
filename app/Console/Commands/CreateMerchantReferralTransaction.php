<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Transactions\TransactionLogic;

use Carbon\Carbon;

class CreateMerchantReferralTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create transaction for merchant with first booking';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionLogic $transaction)
    {
        parent::__construct();
        $this->transaction    = $transaction;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->transaction->createReferralTransaction();
    }
}
